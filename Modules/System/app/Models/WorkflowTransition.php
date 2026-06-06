<?php

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowTransition extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'from_step_id',
        'to_step_id',
        'action_name',
        'action_code',
        'permission',
        'notification_rules',
        'custom_message',
        'field_updates',
    ];

    protected $casts = [
        'notification_rules' => 'json',
        'field_updates' => 'json',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function fromStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'from_step_id');
    }

    public function toStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'to_step_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(WorkflowAssignment::class, 'workflow_transition_id');
    }
}
