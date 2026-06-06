<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PageBlock extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'cms_page_blocks';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'page_section_id',
        'type',
        'heading',
        'subheading',
        'description',
        'items_list',
        'icon_type',
        'icon_class',
        'icon_image',
        'background_color',
        'column_width',
        'badge',
        'video_url',
        'btn_text',
        'btn_link',
        'buttons',
        'sort_order',
        'is_enabled',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'items_list' => 'array',
        'buttons' => 'array',
        'is_enabled' => 'boolean',
    ];

    /**
     * Get the page section that owns the block.
     */
    public function pageSection()
    {
        return $this->belongsTo(PageSection::class, 'page_section_id');
    }

    /**
     * Get the page through the section relationship.
     */
    public function page()
    {
        return $this->hasOneThrough(Page::class, PageSection::class, 'id', 'id', 'page_section_id', 'page_id');
    }

    /**
     * Register media collections for Spatie Media Library.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon_image')->singleFile();
        $this->addMediaCollection('content_image')->singleFile();
    }
}
