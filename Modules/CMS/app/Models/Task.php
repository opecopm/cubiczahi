<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Task extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'cms_tasks';
    protected $primaryKey = 'task_id';

    public $translatable = [
        'task_title',
        'task_description',
    ];

    protected $fillable = [
        'project_id',
        'task_title',
        'task_description',
        'status',
        'due_date',
        'assigned_to',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function assignedMember()
    {
        return $this->belongsTo(ProjectMember::class, 'assigned_to', 'member_id');
    }
}
