<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PageBuilderBlock extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'cms_page_builder_blocks';

    protected $fillable = [
        'column_id',
        'block_type',
        'sort_order',
        'content',
        'settings',
        'css_classes',
        'custom_css'
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'css_classes' => 'array',
        'custom_css' => 'array',
    ];

    // Relationships
    public function column()
    {
        return $this->belongsTo(PageBuilderColumn::class, 'column_id');
    }

    // Media Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('block_images')->singleFile();
    }

    // Helper Methods
    public function duplicate()
    {
        $newBlock = $this->replicate();
        $newBlock->sort_order = $this->getNextSortOrder();
        $newBlock->save();

        // Copy media files if any
        if ($this->hasMedia('block_images')) {
            $media = $this->getFirstMedia('block_images');
            if ($media) {
                $newBlock->addMedia($media->getPath())->toMediaCollection('block_images');
            }
        }

        return $newBlock;
    }

    public function getNextSortOrder()
    {
        return $this->column->blocks()->max('sort_order') + 1;
    }

    public function updateSortOrder($newOrder)
    {
        $this->sort_order = $newOrder;
        $this->save();
    }

    public function getStyleAttributes()
    {
        $styles = [];
        
        if ($this->settings) {
            if (isset($this->settings['text_color'])) {
                $styles[] = "color: {$this->settings['text_color']}";
            }
            if (isset($this->settings['font_size'])) {
                $styles[] = "font-size: {$this->settings['font_size']}px";
            }
            if (isset($this->settings['text_align'])) {
                $styles[] = "text-align: {$this->settings['text_align']}";
            }
            if (isset($this->settings['background_color'])) {
                $styles[] = "background-color: {$this->settings['background_color']}";
            }
            if (isset($this->settings['padding'])) {
                $styles[] = "padding: {$this->settings['padding']}px";
            }
            if (isset($this->settings['margin'])) {
                $styles[] = "margin: {$this->settings['margin']}px";
            }
        }

        return implode('; ', $styles);
    }

    // Block Type Specific Methods
    public function getTextContent()
    {
        return $this->content['text'] ?? '';
    }

    public function getImageUrl()
    {
        if ($this->hasMedia('block_images')) {
            return $this->getFirstMediaUrl('block_images');
        }
        return $this->content['image_url'] ?? '';
    }

    public function getButtonText()
    {
        return $this->content['button_text'] ?? '';
    }

    public function getButtonUrl()
    {
        return $this->content['button_url'] ?? '';
    }

    public function getVideoUrl()
    {
        return $this->content['video_url'] ?? '';
    }

    public function getSpacerHeight()
    {
        return $this->content['height'] ?? 50;
    }

    public function getHtmlContent()
    {
        return $this->content['html'] ?? '';
    }

    // Validation Methods
    public function validateContent()
    {
        switch ($this->block_type) {
            case 'text':
                return !empty($this->content['text']);
            case 'image':
                return !empty($this->getImageUrl());
            case 'button':
                return !empty($this->content['button_text']) && !empty($this->content['button_url']);
            case 'video':
                return !empty($this->content['video_url']);
            case 'spacer':
            case 'divider':
                return true; // Always valid
            case 'html':
                return !empty($this->content['html']);
            default:
                return false;
        }
    }
}


