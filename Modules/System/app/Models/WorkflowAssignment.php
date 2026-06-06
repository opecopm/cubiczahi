<?php

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WorkflowAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'workflow_transition_id',
        'assignable_type',
        'assignable_id',
        'assignment_rule',
        'assignment_value',
        'is_primary',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function transition(): BelongsTo
    {
        return $this->belongsTo(WorkflowTransition::class, 'workflow_transition_id');
    }

    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }
}
