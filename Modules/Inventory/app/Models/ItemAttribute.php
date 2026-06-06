<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class ItemAttribute extends Model
{
    protected $fillable = ['item_id', 'attribute_name_id', 'name', 'slug', 'type', 'display_order', 'is_required', 'is_variant_defining'];

    protected $casts = [
        'is_required' => 'boolean',
        'is_variant_defining' => 'boolean',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function attributeName()
    {
        return $this->belongsTo(ItemAttributeName::class, 'attribute_name_id');
    }

    public function values()
    {
        return $this->hasMany(ItemAttributeValue::class, 'attribute_id')->orderBy('display_order');
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }
}
