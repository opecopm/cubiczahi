<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ItemImage extends Model
{
    protected $fillable = ['item_id', 'path', 'alt_text', 'display_order', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function variantImages()
    {
        return $this->hasMany(VariantImage::class, 'item_image_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
