<?php

namespace Modules\System\Livewire\Workflows;

use App\Livewire\WithModalTrait;
use Livewire\Component;
use Modules\System\Models\Workflow;
use Modules\System\Models\WorkflowAssignment;
use Modules\System\Models\WorkflowInstance;
use Modules\System\Models\WorkflowStep;
use Modules\System\Models\WorkflowTransition;

class Show extends Component
{
    use WithModalTrait;

    public $workflow;

    public $workflowId;

    // Step Management
    public $stepId;

    public $stepName;

    public $stepCode;

    public $isInitial = false;

    public $isFinal = false;

    // Transition Management
    public $transitionId;

    public $fromStepId;

    public $toStepId;

    public $actionName;

    public $actionCode;

    public $permission;

    public $notificationRules = ['notify' => []];

    public $customMessage;

    public $fieldUpdates = [];

    // Assignment Management
    public $assignmentsData = []; // Array of arrays: ['assignment_rule', 'assignable_type', 'assignable_id', 'assignment_value']

    // Workflow Rules (SLA)
    public $escalationRules = [];

    public $matchRules = [];

    public $modalType = 'step'; // 'step' or 'transition'

    public $updateMode = false;

    public function mount($id)
    {
        $this->workflowId = $id;
        $this->loadWorkflow();
    }

    public function loadWorkflow()
    {
        $this->workflow = Workflow::with([
            'steps',
            'transitions.fromStep',
            'transitions.toStep',
            'transitions.assignments.assignable',
        ])->findOrFail($this->workflowId);
        $this->escalationRules = $this->workflow->workflow_rules['escalation'] ?? [];
        $this->matchRules = $this->workflowMatchToRows($this->workflow->workflow_rules['match'] ?? []);
    }

    public function resetInputFields()
    {
        $this->stepId = null;
        $this->stepName = '';
        $this->stepCode = '';
        $this->isInitial = false;
        $this->isFinal = false;

        $this->transitionId = null;
        $this->fromStepId = null;
        $this->toStepId = null;
        $this->actionName = '';
        $this->actionCode = '';
        $this->permission = '';
        $this->notificationRules = ['notify' => []];
        $this->customMessage = '';
        $this->fieldUpdates = [[
            'field' => 'status',
            'value' => '',
        ]];

        $this->assignmentsData = [[
            'assignment_rule' => 'explicit',
            'assignable_type' => 'App\Models\User',
            'assignable_id' => null,
            'assignment_value' => '',
        ]];

        $this->updateMode = false;
    }

    // STEP CRUD
    public function openStepModal($id = null)
    {
        $this->resetInputFields();
        $this->modalType = 'step';
        if ($id) {
            $step = WorkflowStep::findOrFail($id);
            $this->stepId = $step->id;
            $this->stepName = $step->name;
            $this->stepCode = $step->code;
            $this->isInitial = $step->is_initial;
            $this->isFinal = $step->is_final;
            $this->updateMode = true;
        }
        $this->showModal = true;
    }

    public function saveStep()
    {
        $this->validate([
            'stepName' => 'required',
            'stepCode' => 'required',
        ]);

        if ($this->isInitial) {
            // Ensure only one initial step
            WorkflowStep::where('workflow_id', $this->workflowId)->update(['is_initial' => false]);
        }

        WorkflowStep::updateOrCreate(
            ['id' => $this->stepId],
            [
                'workflow_id' => $this->workflowId,
                'name' => $this->stepName,
                'code' => $this->stepCode,
                'is_initial' => $this->isInitial,
                'is_final' => $this->isFinal,
            ]
        );

        session()->flash('message', 'Step saved successfully.');
        $this->closeModal();
        $this->loadWorkflow();
    }

    public function deleteStep($id = null): void
    {
        $stepId = $id ?? $this->stepId;
        if (! $stepId) {
            return;
        }

        $step = WorkflowStep::where('workflow_id', $this->workflowId)->findOrFail($stepId);

        $hasTransitions = WorkflowTransition::where('workflow_id', $this->workflowId)
            ->where(function ($q) use ($stepId) {
                $q->where('from_step_id', $stepId)->orWhere('to_step_id', $stepId);
            })
            ->exists();

        if ($hasTransitions) {
            session()->flash('error', 'Cannot delete this step because it is used in transitions.');

            return;
        }

        $hasInstances = WorkflowInstance::where('workflow_id', $this->workflowId)
            ->where('current_step_id', $stepId)
            ->exists();

        if ($hasInstances) {
            session()->flash('error', 'Cannot delete this step because there are workflow instances currently on it.');

            return;
        }

        $step->delete();

        session()->flash('message', 'Step deleted successfully.');
        $this->closeModal();
        $this->resetInputFields();
        $this->loadWorkflow();
    }

    // TRANSITION CRUD
    public function openTransitionModal($id = null)
    {
        $this->resetInputFields();
        $this->modalType = 'transition';
        if ($id) {
            $transition = WorkflowTransition::findOrFail($id);
            $this->transitionId = $transition->id;
            $this->fromStepId = $transition->from_step_id;
            $this->toStepId = $transition->to_step_id;
            $this->actionName = $transition->action_name;
            $this->actionCode = $transition->action_code;
            $this->permission = $transition->permission;
            $this->notificationRules = $transition->notification_rules ?? ['notify' => []];
            $this->customMessage = $transition->custom_message;
            $this->fieldUpdates = $transition->field_updates ?: [[
                'field' => 'status',
                'value' => '',
            ]];

            // Load all assignments
            $this->assignmentsData = $transition->assignments->map(function ($a) {
                return [
                    'assignment_rule' => $a->assignment_rule === 'field' ? 'model_field' : $a->assignment_rule,
                    'assignable_type' => $a->assignable_type ?? 'App\Models\User',
                    'assignable_id' => $a->assignable_id,
                    'assignment_value' => $a->assignment_value,
                ];
            })->toArray();

            if (empty($this->assignmentsData)) {
                $this->assignmentsData = [[
                    'assignment_rule' => 'explicit',
                    'assignable_type' => 'App\Models\User',
                    'assignable_id' => null,
                    'assignment_value' => '',
                ]];
            }

            $this->updateMode = true;
        }
        $this->showModal = true;
    }

    public function saveTransition()
    {
        $this->validate([
            'toStepId' => 'required',
            'actionName' => 'required',
            'actionCode' => 'required',
        ]);

        $transition = WorkflowTransition::updateOrCreate(
            ['id' => $this->transitionId],
            [
                'workflow_id' => $this->workflowId,
                'from_step_id' => $this->fromStepId ?: null,
                'to_step_id' => $this->toStepId,
                'action_name' => $this->actionName,
                'action_code' => $this->actionCode,
                'permission' => $this->permission,
                'notification_rules' => $this->notificationRules,
                'custom_message' => $this->customMessage,
                'field_updates' => $this->normalizeFieldUpdates($this->fieldUpdates),
            ]
        );

        // Sync Assignment Rules
        $transition->assignments()->delete();
        foreach ($this->assignmentsData as $index => $data) {
            WorkflowAssignment::create([
                'workflow_id' => $this->workflowId,
                'workflow_transition_id' => $transition->id,
                'assignment_rule' => $data['assignment_rule'],
                'assignable_type' => $data['assignment_rule'] === 'explicit' ? $data['assignable_type'] : null,
                'assignable_id' => $data['assignment_rule'] === 'explicit' ? $data['assignable_id'] : null,
                'assignment_value' => $data['assignment_value'],
                'is_primary' => ($index === 0),
            ]);
        }

        session()->flash('message', 'Transition saved successfully.');
        $this->closeModal();
        $this->loadWorkflow();
    }

    public function deleteTransition($id = null): void
    {
        $transitionId = $id ?? $this->transitionId;
        if (! $transitionId) {
            return;
        }

        $transition = WorkflowTransition::where('workflow_id', $this->workflowId)->findOrFail($transitionId);
        $transition->assignments()->delete();
        $transition->delete();

        session()->flash('message', 'Transition deleted successfully.');
        $this->closeModal();
        $this->resetInputFields();
        $this->loadWorkflow();
    }

    public function addFieldUpdateRow(): void
    {
        $this->fieldUpdates[] = [
            'field' => '',
            'value' => '',
        ];
    }

    public function removeFieldUpdateRow($index): void
    {
        unset($this->fieldUpdates[$index]);
        $this->fieldUpdates = array_values($this->fieldUpdates);
        if (empty($this->fieldUpdates)) {
            $this->addFieldUpdateRow();
        }
    }

    protected function normalizeFieldUpdates($fieldUpdates): array
    {
        $rows = is_array($fieldUpdates) ? $fieldUpdates : [];

        return collect($rows)
            ->map(function ($row) {
                return [
                    'field' => trim((string) ($row['field'] ?? '')),
                    'value' => (string) ($row['value'] ?? ''),
                ];
            })
            ->filter(fn ($row) => $row['field'] !== '')
            ->values()
            ->all();
    }

    public function addAssignmentRow()
    {
        $this->assignmentsData[] = [
            'assignment_rule' => 'explicit',
            'assignable_type' => 'App\Models\User',
            'assignable_id' => null,
            'assignment_value' => '',
        ];
    }

    public function removeAssignmentRow($index)
    {
        unset($this->assignmentsData[$index]);
        $this->assignmentsData = array_values($this->assignmentsData);
        if (empty($this->assignmentsData)) {
            $this->addAssignmentRow();
        }
    }

    public function saveSLA()
    {
        $rules = $this->workflow->workflow_rules ?? [];
        $rules['escalation'] = $this->escalationRules;
        $this->workflow->update(['workflow_rules' => $rules]);
        session()->flash('message', 'SLA rules updated successfully.');
    }

    public function addMatchRuleRow(): void
    {
        $this->matchRules[] = [
            'field' => '',
            'value' => '',
        ];
    }

    public function removeMatchRuleRow($index): void
    {
        unset($this->matchRules[$index]);
        $this->matchRules = array_values($this->matchRules);
        if (empty($this->matchRules)) {
            $this->addMatchRuleRow();
        }
    }

    public function saveMatchRules(): void
    {
        $rules = $this->workflow->workflow_rules ?? [];
        $rules['match'] = $this->rowsToWorkflowMatch($this->matchRules);
        $this->workflow->update(['workflow_rules' => $rules]);
        session()->flash('message', 'Workflow match rules updated successfully.');
        $this->loadWorkflow();
    }

    protected function workflowMatchToRows($match): array
    {
        $rows = [];
        if (! is_array($match)) {
            return [[
                'field' => '',
                'value' => '',
            ]];
        }

        foreach ($match as $field => $value) {
            if (is_array($value)) {
                $value = implode(', ', array_map('strval', $value));
            }
            $rows[] = [
                'field' => (string) $field,
                'value' => (string) $value,
            ];
        }

        return empty($rows) ? [[
            'field' => '',
            'value' => '',
        ]] : $rows;
    }

    protected function rowsToWorkflowMatch($rows): array
    {
        $rows = is_array($rows) ? $rows : [];
        $match = [];

        foreach ($rows as $row) {
            $field = trim((string) ($row['field'] ?? ''));
            if ($field === '') {
                continue;
            }

            $raw = trim((string) ($row['value'] ?? ''));
            if ($raw === '') {
                continue;
            }

            if (str_contains($raw, ',')) {
                $match[$field] = array_values(array_filter(array_map('trim', explode(',', $raw)), fn ($v) => $v !== ''));
            } else {
                $match[$field] = $raw;
            }
        }

        return $match;
    }

    public function render()
    {
        return view('system::livewire.workflows.show');
    }
}
