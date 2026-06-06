<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class BannerItem extends Model
{
    use HasTranslations;

    protected $table = 'cms_banner_items';

    protected $fillable = [
        'banner_id',
        'title',
        'subtitle',
        'content',
        'image',
        'link',
        'buttons',   // JSON array of buttons: [{label, url, sort_order}, ...]
        'sort_order',
        'status',
    ];

    // Translatable fields
    public $translatable = ['title', 'subtitle', 'content'];

    // Casts
    protected $casts = [
        'buttons' => 'array',
        'status'  => 'boolean',
    ];

    // Status constants
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * Relation to Banner
     */
    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }

    /**
     * Accessor for readable status
     */
    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    /**
     * 🔹 Accessor to return buttons sorted by sort_order
     */
    public function getSortedButtonsAttribute()
    {
        if (!$this->buttons) {
            return [];
        }

        return collect($this->buttons)
            ->sortBy('sort_order')
            ->values()
            ->toArray();
    }
}
