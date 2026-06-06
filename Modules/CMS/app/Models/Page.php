<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\CMS\Database\Factories\PageFactory;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Page extends Model implements HasMedia
{
    use HasFactory, HasTranslations ,InteractsWithMedia;
    protected $table = 'cms_pages';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title', 'slug', 'content', 'status',
        'template_type', 'template_name', 'parent_id', 'published_at',
        'meta_description', 'meta_keywords', 'canonical_url',
        'og_title', 'og_description', 'og_url', 'og_type', 'og_site_name', 'og_locale',
        'published_time', 'modified_time',
        'twitter_card', 'twitter_title', 'twitter_description', 'breadcrumb_title',
        'subtitle', 'alternative_title', 'breadcrumb_image', 'video_url', 'icon',
        'page_type', 'is_featured'
    ];

    public $translatable = [
        'title', 'content', 'breadcrumb_title', 'subtitle', 'alternative_title',
        'meta_description', 'meta_keywords', 'og_title', 'og_description',
        'twitter_title', 'twitter_description'
    ];

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    /**
     * Get the sections for the page.
     */
    public function sections()
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    public function getFullSlugAttribute()
    {
        $segments = [$this->slug];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($segments, $parent->slug);
            $parent = $parent->parent;
        }

        return implode('/', $segments);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('breadcrumb_image')->singleFile();
        $this->addMediaCollection('icon_image')->singleFile();
    }
}