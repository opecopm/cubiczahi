<?php

namespace Modules\Global\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Selling\Models\SalesOrder;

class ReferenceSchema extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['type', 'model', 'prefix', 'date_prefix', 'reset_period', 'initial_value', 'increment', 'next_value', 'digits', 'status'];

    const STATUS_SELECT = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    public static function generate($type, $manualSerial = null)
    {
        if ($manualSerial) {
            // Check if the manual serial number already exists
            $exists = self::checkIfSerialExists($type, $manualSerial);
            if ($exists) {
                throw new \Exception('Serial number already exists.');
            }

            return $manualSerial;
        }

        $schema = ReferenceSchema::where('type', $type)->firstOrFail();

        if (self::shouldReset($schema)) {
            $schema->next_value = $schema->initial_value;
        }

        $nextNumber = $schema->next_value;
        $schema->next_value += $schema->increment;
        $schema->save();

        $datePrefix = $schema->date_prefix ? Carbon::now()->format($schema->date_prefix) : '';

        return $schema->prefix.$datePrefix.str_pad($nextNumber, $schema->digits ?? 6, '0', STR_PAD_LEFT);
    }

    private static function checkIfSerialExists($type, $serial)
    {
        switch ($type) {
            case 'sales_order':
                return SalesOrder::where('order_number', $serial)->exists();
            case 'purchase_order':
                // return PurchaseOrder::where('order_number', $serial)->exists();
            case 'invoice':
                // return Invoice::where('invoice_number', $serial)->exists();
            default:
                return false;
        }
    }

    public static function getNextReference($type)
    {
        $schema = ReferenceSchema::where('type', $type)->firstOrFail();

        $nextValue = self::shouldReset($schema) ? $schema->initial_value : $schema->next_value;

        $datePrefix = $schema->date_prefix ? Carbon::now()->format($schema->date_prefix) : '';

        return $schema->prefix.$datePrefix.str_pad($nextValue, $schema->digits ?? 6, '0', STR_PAD_LEFT);
    }

    protected static function shouldReset(ReferenceSchema $schema): bool
    {
        $period = $schema->reset_period ?? null;
        if (! $period || $period === 'none') {
            return false;
        }

        $last = $schema->updated_at instanceof Carbon
            ? $schema->updated_at
            : ($schema->created_at instanceof Carbon ? $schema->created_at : Carbon::now());

        $now = Carbon::now();

        return match ($period) {
            'daily' => ! $last->isSameDay($now),
            'monthly' => $last->format('Y-m') !== $now->format('Y-m'),
            'yearly' => $last->year !== $now->year,
            default => false,
        };
    }
}
