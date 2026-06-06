<?php

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WorkflowInstance extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'model_type',
        'model_id',
        'current_step_id',
        'assigned_to_type',
        'assigned_to_id',
        'status',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function currentStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'current_step_id');
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignedTo(): MorphTo
    {
        return $this->morphTo('assignedTo', 'assigned_to_type', 'assigned_to_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WorkflowLog::class);
    }

    public function getTargetReferenceAttribute(): string
    {
        if (! $this->model) {
            return "ID: {$this->model_id} (Deleted)";
        }

        return $this->model->reference ?? $this->model->name ?? "ID: {$this->model_id}";
    }

    public function getTargetUrlAttribute(): ?string
    {
        if (! $this->model) {
            return null;
        }

        // Logic to resolve routes based on model type
        if ($this->model_type === 'Modules\SupportDesk\Models\SupportTicket') {
            return route('supportdesk.support-tickets.index'); // Placeholder or specific route if available
        }

        if ($this->model_type === 'Modules\DMS\Models\Document') {
            return route('dms.documents.show', $this->model_id);
        }

        return null;
    }
}
