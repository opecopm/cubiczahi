<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\CRM\Models\Customer;

class ItemPrice extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'item_id',
        'price',
        'price_type',    // selling | purchase
        'currency',
        'currency_rate',
        'vendor_id',
        'customer_id',
        'date_from',
        'date_to',
        'is_default',    // optional default flag
    ];

    /**
     * Price type labels
     */
    public const PRICE_SELECT = [
        'selling' => 'Selling',
        'purchase' => 'Purchase',
    ];

    /* casts */

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /* -------------------------------------------------
     |  Scopes
     | -------------------------------------------------
     */

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('date_from')
                ->orWhere('date_from', '<=', now());
        })->where(function ($q) {
            $q->whereNull('date_to')
                ->orWhere('date_to', '>=', now());
        });
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId)
            ->where('price_type', 'purchase');
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId)
            ->where('price_type', 'selling');
    }

    /* -------------------------------------------------
     |  Business Logic
     | -------------------------------------------------
     */

    public static function getVendorPrice($itemId, $vendorId)
    {
        return self::where('item_id', $itemId)
            ->forVendor($vendorId)
            ->valid()
            ->orderByDesc('date_from')
            ->value('price');
    }

    public static function getCustomerPrice($itemId, $customerId)
    {
        return self::where('item_id', $itemId)
            ->forCustomer($customerId)
            ->valid()
            ->orderByDesc('date_from')
            ->value('price');
    }

    public static function getPrice($itemId, $vendorId = null, $customerId = null)
    {
        if ($customerId) {
            $customerPrice = self::getCustomerPrice($itemId, $customerId);
            if ($customerPrice !== null) {
                return $customerPrice;
            }
        }

        if ($vendorId) {
            $vendorPrice = self::getVendorPrice($itemId, $vendorId);
            if ($vendorPrice !== null) {
                return $vendorPrice;
            }
        }

        // Fallback → default or first valid price
        return self::where('item_id', $itemId)
            ->valid()
            ->orderByDesc('is_default') // prefer default if set
            ->orderByDesc('date_from')
            ->value('price');
    }

    /* -------------------------------------------------
     |  Relations
     | -------------------------------------------------
     */

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
