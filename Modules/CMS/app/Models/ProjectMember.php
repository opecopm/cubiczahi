<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ProjectMember extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'cms_project_members';
    protected $primaryKey = 'member_id';

    public $translatable = [
        'name',
        'role',
    ];

    protected $fillable = [
        'project_id',
        'user_id',
        'name',
        'role',
        'member_image',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}
