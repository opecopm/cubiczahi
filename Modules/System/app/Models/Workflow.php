<?php

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Business\Models\Company;
use Modules\Business\Models\Location;

class Workflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model_type',
        'company_id',
        'location_id',
        'description',
        'is_active',
        'workflow_rules',
        'initial_notification_rules',
        'initial_custom_message',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'workflow_rules' => 'json',
        'initial_notification_rules' => 'json',
    ];

    public $filterable = [
        'model_type' => ['type' => 'text'],
        'company_id' => ['type' => 'text'],
        'location_id' => ['type' => 'text'],
        'is_active' => [
            'type' => 'select',
            'options' => [
                1 => 'Active',
                0 => 'Inactive',
            ],
        ],
    ];

    public function steps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class);
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class);
    }

    public function instances(): HasMany
    {
        return $this->hasMany(WorkflowInstance::class);
    }

    public function initialStep()
    {
        return $this->steps()->where('is_initial', true)->first();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
