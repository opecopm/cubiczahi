<?php

namespace Modules\Selling\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'invoice_id',
        'item_id',
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
        'rental_start_at',
        'rental_end_at',
        'purchase_order_id',
    ];

    protected $casts = [
        'rental_start_at' => 'datetime',
        'rental_end_at' => 'datetime',
        'is_rental' => 'boolean',
    ];

    /**
     * Accessors for formatted rental dates
     */
    public function getRentalStartAtFormattedAttribute()
    {
        return $this->rental_start_at ? $this->rental_start_at->format('Y-m-d') : null;
    }

    public function getRentalEndAtFormattedAttribute()
    {
        return $this->rental_end_at ? $this->rental_end_at->format('Y-m-d') : null;
    }

    /**
     * Example: Mutator if you want to always store as start of the day / end of the day
     */
    public function setRentalStartAtAttribute($value)
    {
        $this->attributes['rental_start_at'] = $value ? Carbon::parse($value)->startOfDay() : null;
    }

    public function setRentalEndAtAttribute($value)
    {
        $this->attributes['rental_end_at'] = $value ? Carbon::parse($value)->endOfDay() : null;
    }
}
