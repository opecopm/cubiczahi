<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ProjectCategory extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'cms_project_categories';
    protected $primaryKey = 'category_id';

    public $translatable = ['category_name', 'slug'];

    protected $fillable = [
        'category_name',
        'slug',
        'parent_id',
        'icon_class',
        'icon_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(ProjectCategory::class, 'parent_id', 'category_id');
    }

    public function children()
    {
        return $this->hasMany(ProjectCategory::class, 'parent_id', 'category_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'cms_project_category', 'category_id', 'project_id');
    }
}
