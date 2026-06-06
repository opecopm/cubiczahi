<?php

namespace Modules\Selling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sales_order_id',
        'item_id',
        'variant_id',
        'name',
        'description',
        'quantity',
        'unit',
        'price',
        'total_price',
        'discount_type',
        'discount_rate',
        'discount',
        'subtotal',
        'tax_id',
        'tax_rate',
        'tax_name',
        'tax',
        'total',
        'name',
        'description',
        'is_rental',
        'rent_start_at',
        'rent_end_at',
    ];
}
