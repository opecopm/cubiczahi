<?php

namespace Modules\System\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Modules\System\Models\WorkflowInstance;
use Modules\System\Services\WorkflowEngine;

trait HasWorkflow
{
    /**
     * Get the workflow instance associated with the model.
     */
    public function workflowInstance(): MorphOne
    {
        return $this->morphOne(WorkflowInstance::class, 'model');
    }

    /**
     * Start the workflow for the model.
     */
    public function startWorkflow(): ?WorkflowInstance
    {
        return app(WorkflowEngine::class)->initiate($this);
    }

    /**
     * Check if a user can perform an action.
     */
    public function canPerformWorkflowAction(string $actionCode, ?User $user = null): bool
    {
        return app(WorkflowEngine::class)->can($this, $actionCode, $user);
    }

    /**
     * Transition to the next workflow step.
     */
    public function transitWorkflow(string $actionCode, ?string $comment = null, ?User $user = null): bool
    {
        return app(WorkflowEngine::class)->transit($this, $actionCode, $comment, $user);
    }

    /**
     * Get available workflow actions for the model.
     *
     * @return Collection
     */
    public function getAvailableWorkflowActions()
    {
        return app(WorkflowEngine::class)->getAvailableActions($this);
    }

    /**
     * Get the current workflow step name.
     */
    public function getCurrentWorkflowStepNameAttribute(): ?string
    {
        return $this->workflowInstance()->with('currentStep')->first()?->currentStep?->name;
    }
}
