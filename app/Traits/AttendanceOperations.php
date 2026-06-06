<?php

namespace App\Traits;

use Carbon\Carbon;
use Modules\HRM\Models\Attendance;
use Modules\HRM\Models\AttendancePunch;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\EmployeeWorkShift;
use Modules\HRM\Models\WorkShift;

trait AttendanceOperations
{
    /**
     * Compute minutes for attendance using clock in/out and shift break.
     */
    protected function computeAttendanceMinutes(array $data): array
    {
        $total = 0;
        if (! empty($data['clockin_at']) && ! empty($data['clockout_at'])) {
            $in = Carbon::parse($data['clockin_at']);
            $out = Carbon::parse($data['clockout_at']);
            /*if ($out->lte($in)) {
                // Overnight shift handling: clockout on next day
                $out->addDay();
            }*/
            $total = round($in->diffInMinutes($out), 2);
        }
        $shift = $this->resolveEmployeeWorkShift(
            $data['employee_id'] ?? null,
            $data['attendance_date'] ?? null,
            $data['work_shift_id'] ?? null
        );

        $shiftBreak = $shift ? (float) ($shift->break_minutes ?? 0) : 0;
        $recordedBreak = 0.0;
        if (! empty($data['attendance_breaks'])) {
            $recordedBreak = (float) array_sum(array_column($data['attendance_breaks'], 'total_minutes'));
        }
        $workNoDefaultBreak = max(0, $total - $recordedBreak);
        $shiftMinutes = $shift ? (float) (($shift->total_minutes ?? 0) - $shiftBreak) : 0;
        if ($workNoDefaultBreak > $shiftMinutes) {
            $break = max($recordedBreak, $shiftBreak);
        } else {
            $break = $recordedBreak;
        }
        $work = round(max(0, $total - $break), 2);
        $overtime = max(0, $work - $shiftMinutes);
        $work_minutes = $work - $overtime;
        $approved = $work_minutes;

        return [
            'total_minutes' => max(0, $total),
            'total_break_minutes' => max(0, $break),
            'total_work_minutes' => max(0, $work_minutes),
            'approved_work_minutes' => max(0, $approved),
            'overtime' => max(0, $overtime),
            'work_shift_id' => $shift ? $shift->id : null,
        ];
    }

    /**
     * Resolve employee's WorkShift for a given date, honoring explicit payload,
     * active period assignments, default assignment, then the employee base shift.
     */
    protected function resolveEmployeeWorkShift(?int $employeeId, ?string $attendanceDate, ?int $providedWorkShiftId = null): ?WorkShift
    {
        // Explicit work_shift_id in payload wins
        if ($providedWorkShiftId) {
            return WorkShift::find($providedWorkShiftId);
        }

        if (! $employeeId) {
            return null;
        }

        $date = $attendanceDate ? Carbon::parse($attendanceDate)->toDateString() : null;
        // Active period assignment covering the date
        if ($date) {
            $assignment = EmployeeWorkShift::query()
                ->where('employee_id', $employeeId)
                ->where('date_from', '<=', $date)
                ->where(function ($q) use ($date) {
                    $q->whereNull('date_to')->orWhere('date_to', '>=', $date);
                })
                ->orderBy('date_from', 'desc')
                ->first();
            if ($assignment) {
                return WorkShift::find($assignment->work_shift_id);
            }
        }

        // Default assignment
        $default = EmployeeWorkShift::query()
            ->where('employee_id', $employeeId)
            ->where('is_default', true)
            ->latest('date_from')
            ->first();
        if ($default) {
            return WorkShift::find($default->work_shift_id);
        }

        // Fallback: employee base shift
        $employee = Employee::find($employeeId);
        if ($employee && $employee->shift_id) {
            return WorkShift::find($employee->shift_id);
        }

        return null;
    }

    /**
     * Merge computed minutes into payload.
     */
    protected function mergeComputedMinutes(array $payload): array
    {
        return array_merge($payload, $this->computeAttendanceMinutes($payload));
    }

    /**
     * Resolve break minutes from WorkShift.
     */
    protected function getBreakMinutesFromShift(?int $workShiftId): float
    {
        if (! $workShiftId) {
            return (float) ($this->safeGet($this ?? null, 'attendance.total_break_minutes', 0) ?? 0);
        }

        $shift = WorkShift::find($workShiftId);
        if (! $shift) {
            return 0.0;
        }

        if (! is_null($shift->break_minutes)) {
            return (float) $shift->break_minutes;
        }

        return 0.0;
    }

    /**
     * Safe getter for nested arrays (best-effort), returns default if not found.
     */
    protected function safeGet($context, string $path, $default = null)
    {
        $parts = explode('.', $path);
        $value = $context;
        foreach ($parts as $p) {
            if (is_array($value) && array_key_exists($p, $value)) {
                $value = $value[$p];
            } elseif (is_object($value) && isset($value->{$p})) {
                $value = $value->{$p};
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Generate Attendance records by pairing sequential IN/OUT punches.
     * Uses optional public props on the caller: genFromDate, genToDate, genEmployeeId.
     */
    public function generateAttendances(): void
    {
        $from = isset($this->genFromDate) && $this->genFromDate
            ? Carbon::parse($this->genFromDate)->startOfDay()
            : null;
        $to = isset($this->genToDate) && $this->genToDate
            ? Carbon::parse($this->genToDate)->endOfDay()
            : null;

        $query = AttendancePunch::query();
        if (isset($this->genEmployeeId) && ! empty($this->genEmployeeId)) {
            $query->where('employee_id', $this->genEmployeeId);
        }
        if ($from) {
            $query->where('punched_at', '>=', $from);
        }
        if ($to) {
            $query->where('punched_at', '<=', $to);
        }

        // Process punches chronologically to determine First IN and Last OUT
        $punches = $query->orderBy('employee_id')->orderBy('punched_at')->get();

        $employeeData = [];
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($punches as $p) {
            $empId = $p->employee_id;
            $type = strtolower((string) $p->type);
            $isIn = in_array($type, ['in', 'clockin']);
            $isOut = in_array($type, ['out', 'clockout']);
            $punchTime = Carbon::parse($p->punched_at);
            $punchDate = $punchTime->toDateString();

            if (! isset($employeeData[$empId])) {
                $employeeData[$empId] = [
                    'active_date' => null,
                    'records' => [],
                ];
            }

            if ($isIn) {
                // First IN wins: if we don't have a record for this date, create it.
                // If we already have one, ignore this subsequent IN for the same date.
                if (! isset($employeeData[$empId]['records'][$punchDate])) {
                    $employeeData[$empId]['records'][$punchDate] = [
                        'in' => $p,
                        'out' => null,
                    ];
                    $employeeData[$empId]['active_date'] = $punchDate;
                } else {
                    // Update active date just in case, though it should already be set
                    $employeeData[$empId]['active_date'] = $punchDate;
                }
            } elseif ($isOut) {
                // Last OUT wins: update the OUT for the active date
                $activeDate = $employeeData[$empId]['active_date'];

                if ($activeDate && isset($employeeData[$empId]['records'][$activeDate])) {
                    $inPunch = $employeeData[$empId]['records'][$activeDate]['in'];
                    $inTime = Carbon::parse($inPunch->punched_at);

                    // Only update if OUT is after IN
                    if ($punchTime->gt($inTime)) {
                        $employeeData[$empId]['records'][$activeDate]['out'] = $p;
                    }
                } else {
                    // OUT without a preceding IN (orphan OUT), ignore or count as skipped
                    $skipped++;
                }
            }
        }

        // Persist records
        foreach ($employeeData as $empId => $data) {
            foreach ($data['records'] as $date => $record) {
                $inPunch = $record['in'];
                $outPunch = $record['out'];
                $inTime = Carbon::parse($inPunch->punched_at);

                $outTime = $outPunch ? Carbon::parse($outPunch->punched_at) : null;

                $resolvedShift = $this->resolveEmployeeWorkShift($empId, $date, null);
                $employee = Employee::find($empId);

                $payload = [
                    'employee_id' => $empId,
                    'work_shift_id' => $resolvedShift ? $resolvedShift->id : ($employee->shift_id ?? null),
                    'attendance_date' => $date,
                    'clockin_at' => $inTime->toDateTimeString(),
                    'clockout_at' => $outTime ? $outTime->toDateTimeString() : null,
                    'status' => $outTime ? 'present' : 'missing',
                    'is_manual' => false,
                    'clockin_machine_id' => $inPunch->machine_serial ?? null,
                    'clockin_fingerprint_id' => $inPunch->finger_print_id ?? null,
                    'clockout_machine_id' => $outPunch->machine_serial ?? null,
                    'clockout_fingerprint_id' => $outPunch->finger_print_id ?? null,
                ];

                $payload = $this->mergeComputedMinutes($payload);

                $existing = Attendance::query()
                    ->where('employee_id', $empId)
                    ->whereDate('attendance_date', $date)
                    ->first();

                if ($existing) {
                    $existing->update($payload);
                    $updated++;
                } else {
                    Attendance::create($payload);
                    $created++;
                }
            }
        }

        session()->flash('message', "Generated attendances: {$created} created, {$updated} updated, {$skipped} skipped.");
    }
}
