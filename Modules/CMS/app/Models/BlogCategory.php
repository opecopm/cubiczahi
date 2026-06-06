<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class BlogCategory extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'cms_blog_categories';

    public $translatable = ['name'];

    protected $fillable = ['name', 'slug', 'parent_id', 'status'];

    // Auto-generate slug before create/update
    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $base = is_array($category->name) ? ($category->name[app()->getLocale()] ?? reset($category->name)) : $category->name;
                $category->slug = Str::slug($base);
            }
        });

        static::updating(function ($category) {
            if (empty($category->slug)) {
                $base = is_array($category->name) ? ($category->name[app()->getLocale()] ?? reset($category->name)) : $category->name;
                $category->slug = Str::slug($base);
            }
        });
    }

    // Parent category
    public function parent()
    {
        return $this->belongsTo(BlogCategory::class, 'parent_id');
    }

    // Child categories
    public function children()
    {
        return $this->hasMany(BlogCategory::class, 'parent_id');
    }

    // Blogs relationship
    public function blogs()
    {
        return $this->belongsToMany(
            Blog::class,
            'cms_blog_category_pivot',
            'category_id',
            'blog_id'
        );
    }

    // Blog count accessor
    public function getBlogCountAttribute()
    {
        return $this->blogs()->count();
    }
}
