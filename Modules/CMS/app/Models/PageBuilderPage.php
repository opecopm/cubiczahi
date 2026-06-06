<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PageBuilderPage extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    protected $table = 'cms_pages';

    protected $fillable = [
        'slug',
        'title',
        'meta_description',
        'meta_keywords',
        'status',
        'content',
        'template_type',
        'published_at'
    ];

    protected $casts = [
        'title' => 'array',
        'meta_description' => 'array',
        'meta_keywords' => 'array',
        'content' => 'array',
        'published_at' => 'datetime',
    ];

    public $translatable = ['title', 'meta_description', 'meta_keywords'];

    // Relationships
    public function sections()
    {
        return $this->hasMany(PageBuilderSection::class, 'page_id')->orderBy('sort_order');
    }

    // Media Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('page_images')->singleFile();
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Helper Methods
    public function getPageStructure()
    {
        return $this->sections()->with([
            'rows' => function($query) {
                $query->orderBy('sort_order');
            },
            'rows.columns' => function($query) {
                $query->orderBy('sort_order');
            },
            'rows.columns.blocks' => function($query) {
                $query->orderBy('sort_order');
            }
        ])->get();
    }

    public function duplicate()
    {
        $newPage = $this->replicate();
        $newPage->title = array_map(fn($v) => $v . ' (Copy)', (array) $this->title);
        $newPage->slug = $this->slug . '-copy-' . time();
        $newPage->status = 'draft';
        $newPage->save();

        // Duplicate sections and all nested elements
        foreach ($this->sections as $section) {
            $newSection = $section->replicate();
            $newSection->page_id = $newPage->id;
            $newSection->save();

            foreach ($section->rows as $row) {
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
        }

        return $newPage;
    }
}


