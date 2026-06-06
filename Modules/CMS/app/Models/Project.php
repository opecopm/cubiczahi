<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Project extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'cms_projects';
    protected $primaryKey = 'project_id';

    protected $fillable = [
        'project_title',
        'short_description',
        'project_description',
        'category_id',
        'tags',
        'additional_info',
        'icon_class',
        'icon_image',
        'main_image',
        'gallery_images',
        'start_date',
        'end_date',
        'is_upcoming',
        'status',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_upcoming' => 'boolean',
        'is_active' => 'boolean',
        'gallery_images' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'tags' => 'array',
    ];

    public $translatable = [
        'project_title',
        'short_description',
        'project_description',
        'additional_info',
    ];

    public function categories()
    {
        return $this->belongsToMany(ProjectCategory::class, 'cms_project_category', 'project_id', 'category_id')->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id', 'project_id');
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'project_id', 'project_id');
    }
}
