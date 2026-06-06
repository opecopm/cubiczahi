<?php

namespace Modules\Selling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\CRM\Models\Customer;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'customer_id',
        'status',
        'total_price',
        'coupon_code',
        'discount_type',
        'discount_rate',
        'discount',
        'subtotal',
        'tax_id',
        'tax',
        'total',
        'payment_status',
        'currency',
        'currency_rate',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
