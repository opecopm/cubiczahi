<?php

namespace Modules\Selling\Models;

use App\Models\DeliveryMethod;
use App\Traits\TrackUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\CRM\Models\Customer;
use Modules\Global\Models\ReferenceSchema;

class SalesOrder extends Model
{
    use HasFactory, TrackUser;

    public array $filterable = [
        'reference' => ['type' => 'text', 'operator' => 'like'],
        'customer_id' => ['type' => 'text', 'operator' => '='],
        'order_date' => ['type' => 'date', 'operator' => '='],
        'status' => ['type' => 'select', 'operator' => '=', 'options' => self::STATUS_SELECT],
    ];

    /**
     * The attributes that are mass assignables.
     */
    protected $fillable = [
        'reference',
        'company_id',
        'customer_id',
        'total_price',
        'discount',
        'delivery_fees',
        'subtotal',
        'tax_id',
        'tax',
        'total',
        'status',
        'order_date',
        'delivery_date',
        'delivery_method_id',
        'currency',
        'currency_rate',
        'created_by',
        'updated_by',
    ];

    const STATUS_SELECT = [
        'draft' => 'Draft',
        'new' => 'New',
        'confirmed' => 'Confirmed',
        'processing' => 'Processing',
        'ready' => 'Ready for Pickup',
        'picked_up' => 'Picked Up',
        'delivered' => 'Delivered',
        'canceled' => 'Canceled',
    ];

    public function getAllowedNextStatuses(): array
    {
        return match ($this->status) {
            'draft' => ['confirmed', 'canceled'],
            'new' => ['confirmed', 'canceled'],
            'confirmed' => ['processing', 'canceled'],
            'processing' => ['ready', 'picked_up', 'delivered'],
            'ready' => ['delivered', 'picked_up'],
            'picked_up', 'delivered', 'canceled' => [],
            default => [],
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'draft', 'new' => 'bg-secondary-lt',
            'confirmed' => 'bg-info-lt',
            'processing' => 'bg-warning-lt',
            'ready' => 'bg-teal-lt',
            'picked_up', 'delivered' => 'bg-success-lt',
            'canceled' => 'bg-danger-lt',
            default => 'bg-secondary-lt',
        };
    }
    
    public function getStatusLabel(): string
    {
        return self::STATUS_SELECT[$this->status] ?? ucfirst($this->status);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($record) {
            $record->reference = ReferenceSchema::generate('sales_order');

            // Auto-assign company_id from logged-in user’s default company
            if (auth()->check() && auth()->user()->defaultCompany()) {
                $record->company_id = auth()->user()->defaultCompany()->id;
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class, 'sales_order_id');
    }

    public function deliveryMethod()
    {
        return $this->belongsTo(DeliveryMethod::class);
    }

    public function company()
    {
        return $this->belongsTo(\Modules\Business\Models\Company::class);
    }

    public function invoice()
    {
        return $this->hasOne(SalesInvoice::class, 'sales_order_id');
    }
}
