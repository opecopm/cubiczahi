<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PageSection extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'cms_page_sections';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'page_id',
        'title',
        'subtitle',
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
        'form_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'items_list' => 'array',
        'buttons' => 'array',
        'is_enabled' => 'boolean',
        'form_id' => 'integer',
    ];

    /**
     * Get the form associated with the section.
     */
    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    /**
     * Get the page that owns the section.
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the blocks for the section.
     */
    public function blocks()
    {
        return $this->hasMany(PageBlock::class, 'page_section_id')->orderBy('sort_order');
    }

    /**
     * Register media collections for Spatie Media Library.
     */
    public function registerMediaCollections(): void
    {
        // Spatie media collections can remain if still used in code,
        // though schema suggests icon_image is now a string field.
        // If icon_image string is for storing path directly, we might not need this collection for *that* field,
        // but keeping it doesn't hurt if we want to use addMedia($file)->toMediaCollection('icon_image').
        $this->addMediaCollection('icon_image')->singleFile();
        $this->addMediaCollection('content_image')->singleFile();
    }
}
