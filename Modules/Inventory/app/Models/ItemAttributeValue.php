<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class ItemAttributeValue extends Model
{
    protected $fillable = ['attribute_id', 'value', 'hex_code', 'image_url', 'price_modifier', 'display_order'];

    protected $casts = [
        'price_modifier' => 'decimal:2',
    ];

    public function attribute()
    {
        return $this->belongsTo(ItemAttribute::class, 'attribute_id');
    }

    public function variants()
    {
        return $this->belongsToMany(ItemVariant::class, 'variant_attribute_values', 'attribute_value_id', 'variant_id');
    }
}
