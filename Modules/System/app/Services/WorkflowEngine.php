<?php

namespace Modules\System\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Modules\System\Models\Workflow;
use Modules\System\Models\WorkflowAssignment;
use Modules\System\Models\WorkflowInstance;
use Modules\System\Models\WorkflowLog;
use Modules\System\Models\WorkflowTransition;
use Modules\System\Notifications\WorkflowNotification;

class WorkflowEngine
{
    /**
     * Initiate a workflow for a model.
     */
    public function initiate(Model $model, ?Workflow $workflow = null): ?WorkflowInstance
    {
        if (! $workflow) {
            $workflow = $this->resolveWorkflow($model);
        }

        if (! $workflow) {
            return null;
        }

        $initialStep = $workflow->initialStep();
        if (! $initialStep) {
            return null;
        }

        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'current_step_id' => $initialStep->id,
            'status' => 'active',
        ]);

        // Trigger initial notifications
        if ($workflow->initial_notification_rules) {
            $this->triggerNotifications($model, $workflow->initial_notification_rules, $workflow->initial_custom_message, null, null, Auth::user());
        }

        return $instance;
    }

    /**
     * Check if a user can perform an action on a model's workflow.
     */
    public function can(Model $model, string $actionCode, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();
        if (! $user) {
            return false;
        }

        $instance = $this->getInstance($model);
        if (! $instance || $instance->status !== 'active') {
            return false;
        }

        $transition = WorkflowTransition::where('workflow_id', $instance->workflow_id)
            ->where('action_code', $actionCode)
            ->where(function ($q) use ($instance) {
                $q->where('from_step_id', $instance->current_step_id)
                    ->orWhereNull('from_step_id');
            })
            ->orderByRaw('from_step_id is null')
            ->first();

        if (! $transition) {
            return false;
        }

        // 1. Check Explicit Assignee (if set)
        if ($instance->assigned_to_id) {
            if ($instance->assigned_to_type === get_class($user) && $instance->assigned_to_id == $user->id) {
                return true;
            }

            // Check if assigned to a role the user has
            if ($instance->assigned_to_type === 'Spatie\Permission\Models\Role') {
                $role = DB::table('roles')->where('id', $instance->assigned_to_id)->first();
                if ($role && $user->hasRole($role->name)) {
                    return true;
                }
            }

            // Check if assigned to a team the user belongs to
            if ($instance->assigned_to_type === 'Modules\IAM\Models\Team') {
                if (method_exists($user, 'teams') && $user->teams()->where('teams.id', $instance->assigned_to_id)->exists()) {
                    return true;
                }
            }

            // If explicitly assigned but NOT to this user/their groups,
            // should we still allow global permission holders?
            // For now, let's allow permission fallback unless we want "Strict Mode".
        }

        // 2. Check Permission Fallback
        if ($transition->permission) {
            return $user->can($transition->permission);
        }

        return true;
    }

    /**
     * Transit a model to the next step.
     */
    public function transit(Model $model, string $actionCode, ?string $comment = null, ?User $user = null, array $attachments = []): bool
    {
        $user = $user ?? Auth::user();

        $instance = $this->getInstance($model);
        if (! $instance || ! $this->can($model, $actionCode, $user)) {
            return false;
        }

        $transition = WorkflowTransition::where('workflow_id', $instance->workflow_id)
            ->where('action_code', $actionCode)
            ->where(function ($q) use ($instance) {
                $q->where('from_step_id', $instance->current_step_id)
                    ->orWhereNull('from_step_id');
            })
            ->orderByRaw('from_step_id is null')
            ->first();

        if (! $transition) {
            return false;
        }

        DB::transaction(function () use ($instance, $transition, $actionCode, $comment, $user, $model, $attachments) {
            $fromStepId = $instance->current_step_id;

            // Resolve next assignee
            $assignment = $this->resolveNextAssignee($transition, $model, $user);

            $instance->update([
                'current_step_id' => $transition->to_step_id,
                'assigned_to_type' => $assignment['type'] ?? null,
                'assigned_to_id' => $assignment['id'] ?? null,
                'status' => $transition->toStep->is_final ? 'completed' : 'active',
            ]);

            $workflowLog = WorkflowLog::create([
                'workflow_instance_id' => $instance->id,
                'user_id' => $user->id,
                'from_step_id' => $fromStepId,
                'to_step_id' => $transition->to_step_id,
                'action_code' => $actionCode,
                'comment' => $comment,
            ]);

            if (method_exists($model, 'recordWorkflowDiscussion')) {
                $model->recordWorkflowDiscussion($instance, $workflowLog, $transition, $user, $comment, $attachments);
            }

            if ($transition->field_updates) {
                $this->applyTransitionFieldUpdates($model, $transition->field_updates, $actionCode, $user);
            }

            // Trigger transition notifications
            if ($transition->notification_rules) {
                $this->triggerNotifications($model, $transition->notification_rules, $transition->custom_message, $actionCode, $transition, $user);
            }
        });

        return true;
    }

    /**
     * Resolve recipients and send notifications.
     */
    protected function applyTransitionFieldUpdates(Model $model, $fieldUpdates, ?string $actionCode, ?User $user): void
    {
        $rows = is_array($fieldUpdates) ? $fieldUpdates : json_decode($fieldUpdates, true);
        if (! is_array($rows) || empty($rows)) {
            return;
        }

        $updates = [];
        foreach ($rows as $row) {
            $field = trim((string) ($row['field'] ?? ''));
            if ($field === '') {
                continue;
            }

            $rawValue = $row['value'] ?? null;
            if ($rawValue === null) {
                $updates[$field] = null;

                continue;
            }

            $value = $this->replacePlaceholders((string) $rawValue, $model, $actionCode, $user);
            $updates[$field] = $this->castFieldUpdateValue($value);
        }

        if (empty($updates)) {
            return;
        }

        $model->fill($updates);
        if ($model->isDirty()) {
            $model->save();
        }
    }

    protected function castFieldUpdateValue(string $value): mixed
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            return $value;
        }

        if ($trimmed === 'true') {
            return true;
        }

        if ($trimmed === 'false') {
            return false;
        }

        if (is_numeric($trimmed)) {
            return str_contains($trimmed, '.') ? (float) $trimmed : (int) $trimmed;
        }

        return $value;
    }

    protected function triggerNotifications(
        Model $model,
        $rules,
        ?string $messageTemplate,
        ?string $actionCode = null,
        ?WorkflowTransition $transition = null,
        ?User $actorUser = null
    ) {
        $recipients = collect();
        $emailRecipients = collect();
        $rules = is_array($rules) ? $rules : json_decode($rules, true);
        $notifyList = $rules['notify'] ?? [];
        if (empty($notifyList)) {
            return;
        }

        foreach ($notifyList as $key) {
            if ($key === 'customer' && method_exists($model, 'requester')) {
                $requester = $model->requester;
                if ($requester && method_exists($requester, 'notify')) {
                    $recipients->push($requester);
                } elseif ($requester && ! empty($requester->email)) {
                    $emailRecipients->push((string) $requester->email);
                }
            } elseif ($key === 'creator') {
                $creatorId = $model->created_by ?? $model->user_id ?? null;
                if ($creatorId) {
                    $recipients->push(User::find($creatorId));
                }
            } elseif ($key === 'transition_assignees' && $transition) {
                $transition->loadMissing('assignments');

                $userIds = [];
                foreach ($transition->assignments as $assignment) {
                    if (($assignment->assignment_rule ?? '') !== 'explicit') {
                        continue;
                    }

                    $type = (string) ($assignment->assignable_type ?? '');
                    $id = $assignment->assignable_id;
                    if (! $type || ! $id) {
                        continue;
                    }

                    if ($type === User::class) {
                        $userIds[] = $id;

                        continue;
                    }

                    if ($type === 'Spatie\\Permission\\Models\\Role') {
                        $role = DB::table('roles')->where('id', $id)->first();
                        if ($role) {
                            $recipients = $recipients->merge(User::role($role->name)->get());
                        }

                        continue;
                    }

                    if ($type === 'Modules\\IAM\\Models\\Team' && class_exists($type)) {
                        $team = $type::with('members.user')->find($id);
                        if ($team && isset($team->members)) {
                            $recipients = $recipients->merge(
                                collect($team->members)->map(fn ($m) => $m->user)->filter()
                            );
                        }

                        continue;
                    }
                }

                if (! empty($userIds)) {
                    $recipients = $recipients->merge(User::whereIn('id', array_unique($userIds))->get());
                }
            } elseif ($key === 'assignee') {
                $instance = $this->getInstance($model);
                if ($instance && $instance->assigned_to_id) {
                    if ($instance->assigned_to_type === User::class) {
                        $recipients->push(User::find($instance->assigned_to_id));
                    } elseif ($instance->assigned_to_type === 'Spatie\Permission\Models\Role') {
                        $role = DB::table('roles')->where('id', $instance->assigned_to_id)->first();
                        if ($role) {
                            $recipients = $recipients->merge(User::role($role->name)->get());
                        }
                    } elseif ($instance->assigned_to_type === 'Modules\IAM\Models\Team') {
                        // Logic to get users in a team
                        $team = DB::table('teams')->where('id', $instance->assigned_to_id)->first();
                        // This usually requires a Team model or direct DB query
                    }
                }
            } elseif (str_starts_with($key, 'role:')) {
                $roleName = str_replace('role:', '', $key);
                $recipients = $recipients->merge(User::role($roleName)->get());
            } elseif (str_starts_with($key, 'location_role:')) {
                $roleName = str_replace('location_role:', '', $key);
                if (isset($model->location_id)) {
                    $recipients = $recipients->merge(
                        User::role($roleName)->where('location_id', $model->location_id)->get()
                    );
                }
            }
        }

        $recipients = $recipients->unique('id')->filter();
        $emailRecipients = $emailRecipients->filter()->unique()->values();
        if ($recipients->isEmpty() && $emailRecipients->isEmpty()) {
            return;
        }

        $template = trim((string) ($messageTemplate ?? ''));
        if ($template === '') {
            $template = '{model} {reference}: {action} → {status}';
        }

        $message = $this->replacePlaceholders($template, $model, $actionCode, $actorUser);

        $reference = $model->reference ?? $model->id;
        $modelName = class_basename($model);
        $subject = "OPECO ERP - {$modelName} {$reference} created/updated";

        $notification = new WorkflowNotification($model, $message, $subject);

        if ($recipients->isNotEmpty()) {
            Notification::sendNow($recipients, $notification);
        }

        foreach ($emailRecipients as $email) {
            Notification::route('mail', $email)->notifyNow($notification);
        }
    }

    /**
     * Replace placeholders like {reference}, {status}, {action} in the message.
     */
    protected function replacePlaceholders(string $message, Model $model, ?string $actionCode = null, ?User $user = null): string
    {
        $instance = $this->getInstance($model);

        $placeholders = [
            '{reference}' => $model->reference ?? $model->id,
            '{status}' => $instance?->currentStep?->name ?? 'Unknown',
            '{action}' => $actionCode ?? 'Update',
            '{model}' => class_basename($model),
            '{user_id}' => $user?->id ?? '',
            '{user_name}' => $user?->name ?? '',
            '{now}' => now()->toDateTimeString(),
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $message);
    }

    /**
     * Resolve the appropriate workflow for a model based on hierarchy.
     */
    public function resolveWorkflow(Model $model): ?Workflow
    {
        $modelType = get_class($model);
        $companyId = $model->company_id ?? null;
        $locationId = $model->location_id ?? null;
        $projectId = $model->project_id ?? null;

        $pick = function ($query) use ($model) {
            return $query->get()->first(fn ($wf) => $this->matchesWorkflowRules($wf, $model));
        };

        // 1. Exact Match: Company + Location + Project
        $workflow = $pick(
            Workflow::where('model_type', $modelType)
                ->where('is_active', true)
                ->where('company_id', $companyId)
                ->where('location_id', $locationId)
                ->where('project_id', $projectId)
        );
        if ($workflow) {
            return $workflow;
        }

        // 2. Location Match: Company + Location (Any Project)
        $workflow = $pick(
            Workflow::where('model_type', $modelType)
                ->where('is_active', true)
                ->where('company_id', $companyId)
                ->where('location_id', $locationId)
                ->whereNull('project_id')
        );
        if ($workflow) {
            return $workflow;
        }

        // 3. Company Match: Company (Any Location, Any Project)
        $workflow = $pick(
            Workflow::where('model_type', $modelType)
                ->where('is_active', true)
                ->where('company_id', $companyId)
                ->whereNull('location_id')
                ->whereNull('project_id')
        );
        if ($workflow) {
            return $workflow;
        }

        // 4. Global Fallback: Only Model Type (Rest Null)
        return $pick(
            Workflow::where('model_type', $modelType)
                ->where('is_active', true)
                ->whereNull('company_id')
                ->whereNull('location_id')
                ->whereNull('project_id')
        );
    }

    protected function matchesWorkflowRules(Workflow $workflow, Model $model): bool
    {
        $rules = $workflow->workflow_rules ?? [];
        $match = $rules['match'] ?? null;
        if (! is_array($match) || empty($match)) {
            return true;
        }

        foreach ($match as $key => $expected) {
            $actual = $this->getModelValueByPath($model, (string) $key);

            if (is_array($expected)) {
                if (! in_array((string) $actual, array_map('strval', $expected), true)) {
                    return false;
                }

                continue;
            }

            if ((string) $actual !== (string) $expected) {
                return false;
            }
        }

        return true;
    }

    protected function getModelValueByPath($root, string $path)
    {
        $current = $root;
        foreach (explode('.', $path) as $segment) {
            if ($current === null) {
                return null;
            }

            if (is_array($current)) {
                $current = $current[$segment] ?? null;

                continue;
            }

            if (is_object($current)) {
                $current = $current->{$segment} ?? null;

                continue;
            }

            return null;
        }

        return $current;
    }

    /**
     * Get the workflow instance for a model.
     */
    public function getInstance(Model $model): ?WorkflowInstance
    {
        return WorkflowInstance::where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->first();
    }

    /**
     * Get available transitions for a model.
     *
     * @return Collection
     */
    public function getAvailableActions(Model $model)
    {
        $instance = $this->getInstance($model);
        if (! $instance || $instance->status !== 'active') {
            return collect();
        }

        return WorkflowTransition::where('workflow_id', $instance->workflow_id)
            ->where(function ($q) use ($instance) {
                $q->where('from_step_id', $instance->current_step_id)
                    ->orWhereNull('from_step_id');
            })
            ->orderByRaw('from_step_id is null')
            ->get();
    }

    /**
     * Resolve the next assignee based on transition rules.
     */
    protected function resolveNextAssignee(WorkflowTransition $transition, Model $model, ?User $currentUser): array
    {
        $primaryRule = WorkflowAssignment::where('workflow_transition_id', $transition->id)
            ->where('is_primary', true)
            ->first();

        if (! $primaryRule) {
            return ['type' => null, 'id' => null];
        }

        switch ($primaryRule->assignment_rule) {
            case 'explicit':
                return [
                    'type' => $primaryRule->assignable_type,
                    'id' => $primaryRule->assignable_id,
                ];

            case 'creator':
                $creatorId = $model->created_by ?? $model->user_id ?? null;

                return [
                    'type' => User::class,
                    'id' => $creatorId,
                ];

            case 'model_field':
            case 'field':
                $fieldName = $primaryRule->assignment_value;

                return [
                    'type' => User::class, // Assuming field contains a User ID
                    'id' => $model->$fieldName ?? null,
                ];

            default:
                return ['type' => null, 'id' => null];
        }
    }
}
