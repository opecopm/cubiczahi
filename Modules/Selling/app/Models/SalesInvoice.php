<?php

namespace Modules\Selling\Models;

use App\Traits\BelongsToDefaultCompany;
use App\Traits\TrackUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Business\Models\Company;
use Modules\CRM\Models\Customer;
use Modules\Global\Models\ReferenceSchema;

class SalesInvoice extends Model
{
    use BelongsToDefaultCompany, HasFactory, TrackUser;

    public array $filterable = [
        'reference' => ['type' => 'text', 'operator' => 'like'],
        'customer_id' => ['type' => 'text', 'operator' => '='],
        'invoice_date' => ['type' => 'date', 'operator' => '='],
        'status' => ['type' => 'select', 'operator' => '=', 'options' => self::STATUS_SELECT],
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'reference',
        'customer_id',
        'total_price',
        'discount',
        'subtotal',
        'tax',
        'total',
        'paid_amount',
        'due_amount',
        'status',
        'invoice_date',
        'due_date',
        'currency',
        'currency_rate',
        'sales_order_id',
        'purchase_invoice_id',
    ];

    const STATUS_SELECT = [
        'draft' => 'Draft',
        'final' => 'Final',
        'canceled' => 'Canceled',
    ];

    public function getAllowedNextStatuses(): array
    {
        return match ($this->status) {
            'draft' => ['final', 'canceled'],
            'final' => [],
            'canceled' => [],
            default => [],
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'draft' => 'bg-secondary-lt',
            'final' => 'bg-success-lt',
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
            $record->reference = ReferenceSchema::generate('sales_invoice');
        });

        static::updating(function ($record) {
            // Prevent overriding reference on update
            unset($record->reference);
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'invoice_id');
    }
}
