<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageBuilderColumn extends Model
{
    use HasFactory;

    protected $table = 'cms_page_builder_columns';

    protected $fillable = [
        'row_id',
        'sort_order',
        'width',
        'settings',
        'css_classes',
        'custom_css'
    ];

    protected $casts = [
        'settings' => 'array',
        'css_classes' => 'array',
        'custom_css' => 'array',
    ];

    // Relationships
    public function row()
    {
        return $this->belongsTo(PageBuilderRow::class, 'row_id');
    }

    public function blocks()
    {
        return $this->hasMany(PageBuilderBlock::class, 'column_id')->orderBy('sort_order');
    }

    // Helper Methods
    public function duplicate()
    {
        $newColumn = $this->replicate();
        $newColumn->sort_order = $this->getNextSortOrder();
        $newColumn->save();

        // Duplicate blocks
        foreach ($this->blocks as $block) {
            $newBlock = $block->replicate();
            $newBlock->column_id = $newColumn->id;
            $newBlock->save();
        }

        return $newColumn;
    }

    public function getNextSortOrder()
    {
        return $this->row->columns()->max('sort_order') + 1;
    }

    public function updateSortOrder($newOrder)
    {
        $this->sort_order = $newOrder;
        $this->save();
    }

    public function updateWidth($newWidth)
    {
        // Validate that total width doesn't exceed 12
        $currentTotal = $this->row->columns()->where('id', '!=', $this->id)->sum('width');
        if (($currentTotal + $newWidth) <= 12) {
            $this->width = $newWidth;
            $this->save();
            return true;
        }
        return false;
    }

    public function getBootstrapClass()
    {
        return "col-md-{$this->width}";
    }

    public function getStyleAttributes()
    {
        $styles = [];
        
        if ($this->settings) {
            if (isset($this->settings['background_color'])) {
                $styles[] = "background-color: {$this->settings['background_color']}";
            }
            if (isset($this->settings['padding_top'])) {
                $styles[] = "padding-top: {$this->settings['padding_top']}px";
            }
            if (isset($this->settings['padding_bottom'])) {
                $styles[] = "padding-bottom: {$this->settings['padding_bottom']}px";
            }
            if (isset($this->settings['padding_left'])) {
                $styles[] = "padding-left: {$this->settings['padding_left']}px";
            }
            if (isset($this->settings['padding_right'])) {
                $styles[] = "padding-right: {$this->settings['padding_right']}px";
            }
        }

        return implode('; ', $styles);
    }
}


