<?php

namespace Modules\Selling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Item;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'item_id',
        'variant_id',
        'quantity',
        'unit',
        'price',
        'total_price',
        'coupon_code',
        'discount_type',
        'discount_rate',
        'discount',
        'subtotal',
        'tax_id',
        'tax_name',
        'tax_rate',
        'tax',
        'total',
        'name',
        'description',
        'is_rental',
        'rental_start_at',
        'rental_end_at'
    ];

    protected $casts = [
        'is_rental' => 'boolean',
        'rental_start_at' => 'datetime',
        'rental_end_at' => 'datetime',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
