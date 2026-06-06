<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ItemVariant extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'note'];

    protected $fillable = [
        'item_id',
        'attribute_id',
        'name',
        'note',
        'price_difference',
        'is_default',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'price_difference' => 'decimal:2',
        'is_default'       => 'boolean',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function attribute()
    {
        return $this->belongsTo(ItemAttributeName::class, 'attribute_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function formattedPriceDifference(): string
    {
        if ($this->price_difference == 0) {
            return '';
        }

        return ($this->price_difference > 0 ? '+' : '') . number_format($this->price_difference, 2);
    }
}
