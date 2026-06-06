<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageBuilderRow extends Model
{
    use HasFactory;

    protected $table = 'cms_page_builder_rows';

    protected $fillable = [
        'section_id',
        'sort_order',
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
    public function section()
    {
        return $this->belongsTo(PageBuilderSection::class, 'section_id');
    }

    public function columns()
    {
        return $this->hasMany(PageBuilderColumn::class, 'row_id')->orderBy('sort_order');
    }

    // Helper Methods
    public function duplicate()
    {
        $newRow = $this->replicate();
        $newRow->sort_order = $this->getNextSortOrder();
        $newRow->save();

        // Duplicate columns and blocks
        foreach ($this->columns as $column) {
            $newColumn = $column->replicate();
            $newColumn->row_id = $newRow->id;
            $newColumn->save();

            foreach ($column->blocks as $block) {
                $newBlock = $block->replicate();
                $newBlock->column_id = $newColumn->id;
                $newBlock->save();
            }
        }

        return $newRow;
    }

    public function getNextSortOrder()
    {
        return $this->section->rows()->max('sort_order') + 1;
    }

    public function updateSortOrder($newOrder)
    {
        $this->sort_order = $newOrder;
        $this->save();
    }

    public function validateColumnWidths()
    {
        $totalWidth = $this->columns()->sum('width');
        return $totalWidth <= 12;
    }

    public function getStyleAttributes()
    {
        $styles = [];
        
        if ($this->settings) {
            if (isset($this->settings['padding_top'])) {
                $styles[] = "padding-top: {$this->settings['padding_top']}px";
            }
            if (isset($this->settings['padding_bottom'])) {
                $styles[] = "padding-bottom: {$this->settings['padding_bottom']}px";
            }
            if (isset($this->settings['margin_top'])) {
                $styles[] = "margin-top: {$this->settings['margin_top']}px";
            }
            if (isset($this->settings['margin_bottom'])) {
                $styles[] = "margin-bottom: {$this->settings['margin_bottom']}px";
            }
            if (isset($this->settings['text_align'])) {
                $styles[] = "text-align: {$this->settings['text_align']}";
            }
        }

        return implode('; ', $styles);
    }
}


