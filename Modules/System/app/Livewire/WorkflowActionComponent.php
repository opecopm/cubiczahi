<?php

namespace Modules\System\Livewire;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\System\Services\WorkflowEngine;

class WorkflowActionComponent extends Component
{
    use WithFileUploads;

    public $model;

    public $modelId;

    public $modelType;

    public bool $showLogs = true;

    public bool $autoStart = true;

    public $instance;

    public $availableActions;

    public $comment;

    public array $actionAttachments = [];

    public $showCommentModal = false;

    public $selectedActionCode;

    public $selectedActionName;

    public ?string $targetUrl = null;

    public $lastLog = null;

    public bool $showTimeline = false;

    public array $timelineLogs = [];

    public function mount(Model $model, bool $autoStart = true, bool $showLogs = true)
    {
        $this->model = $model;
        $this->modelId = $model->id;
        $this->modelType = get_class($model);
        $this->autoStart = $autoStart;
        $this->showLogs = $showLogs;
        $this->loadWorkflow();
    }

    public function loadWorkflow()
    {
        $engine = app(WorkflowEngine::class);
        $this->instance = $engine->getInstance($this->model);

        if (! $this->instance && $this->autoStart) {
            $this->instance = $engine->initiate($this->model);
        }

        if ($this->instance) {
            $this->instance->loadMissing(['workflow.steps', 'currentStep', 'model', 'assignedTo']);
            $this->targetUrl = $this->instance->target_url;
            $this->lastLog = $this->instance->logs()->with(['user', 'fromStep', 'toStep'])->latest()->first();
            if ($this->showTimeline) {
                $this->loadTimeline();
            }
            $this->availableActions = $engine->getAvailableActions($this->model)
                ->filter(fn ($a) => $engine->can($this->model, $a->action_code))
                ->values();
        } else {
            $this->targetUrl = null;
            $this->lastLog = null;
            $this->timelineLogs = [];
            $this->availableActions = collect();
        }
    }

    public function toggleTimeline(): void
    {
        $this->showTimeline = ! $this->showTimeline;
        if ($this->showTimeline) {
            $this->loadTimeline();
        }
    }

    protected function loadTimeline(): void
    {
        if (! $this->instance) {
            $this->timelineLogs = [];

            return;
        }

        $logs = $this->instance->logs()
            ->with(['user', 'fromStep', 'toStep'])
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        $this->timelineLogs = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'action_code' => $log->action_code,
                'user_name' => $log->user?->name ?? 'System',
                'user_initials' => $log->user ? $log->user->initials() : 'SY',
                'from_step' => $log->fromStep?->name,
                'to_step' => $log->toStep?->name,
                'comment' => $log->comment,
                'created_at' => $log->created_at?->format('Y-m-d H:i'),
            ];
        })->toArray();
    }

    public function confirmAction($actionCode, $actionName)
    {
        $this->selectedActionCode = $actionCode;
        $this->selectedActionName = $actionName;
        $this->comment = '';
        $this->actionAttachments = [];
        $this->showCommentModal = true;
    }

    public function performAction()
    {
        $engine = app(WorkflowEngine::class);
        $success = $engine->transit($this->model, $this->selectedActionCode, $this->comment, null, $this->actionAttachments);

        if ($success) {
            session()->flash('message', "Action '{$this->selectedActionName}' performed successfully.");
            $this->showCommentModal = false;
            $this->actionAttachments = [];
            $this->loadWorkflow();

            // Dispatch event to parent to refresh the detail page if needed
            $this->dispatch('workflow-updated');
        } else {
            session()->flash('error', 'Failed to perform action. You might not have permission.');
        }
    }

    public function render()
    {
        return view('system::livewire.components.workflow-action-component');
    }
}
