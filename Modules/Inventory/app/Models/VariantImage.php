<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VariantImage extends Model
{
    protected $fillable = ['variant_id', 'item_image_id', 'path', 'alt_text', 'display_order', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean'];

    public function variant()
    {
        return $this->belongsTo(ItemVariant::class, 'variant_id');
    }

    public function itemImage()
    {
        return $this->belongsTo(ItemImage::class, 'item_image_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
