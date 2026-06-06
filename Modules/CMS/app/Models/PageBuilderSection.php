<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageBuilderSection extends Model
{
    use HasFactory;

    protected $table = 'cms_page_builder_sections';

    protected $fillable = [
        'page_id',
        'section_type',
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
    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function rows()
    {
        return $this->hasMany(PageBuilderRow::class, 'section_id')->orderBy('sort_order');
    }

    // Helper Methods
    public function duplicate()
    {
        $newSection = $this->replicate();
        $newSection->sort_order = $this->getNextSortOrder();
        $newSection->save();

        // Duplicate rows and all nested elements
        foreach ($this->rows as $row) {
            $newRow = $row->replicate();
            $newRow->section_id = $newSection->id;
            $newRow->save();

            foreach ($row->columns as $column) {
                $newColumn = $column->replicate();
                $newColumn->row_id = $newRow->id;
                $newColumn->save();

                foreach ($column->blocks as $block) {
                    $newBlock = $block->replicate();
                    $newBlock->column_id = $newColumn->id;
                    $newBlock->save();
                }
            }
        }

        return $newSection;
    }

    public function getNextSortOrder()
    {
        return $this->page->sections()->max('sort_order') + 1;
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
            if (isset($this->settings['background_color'])) {
                $styles[] = "background-color: {$this->settings['background_color']}";
            }
            if (isset($this->settings['background_image'])) {
                $styles[] = "background-image: url('{$this->settings['background_image']}')";
                $styles[] = "background-size: cover";
                $styles[] = "background-position: center";
            }
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
        }

        return implode('; ', $styles);
    }
}


