<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ItemAttributeName extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'attribute_names';

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'slug',
        'is_required',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ItemVariant::class, 'attribute_id');
    }

    public function variantsForItem(int $itemId)
    {
        return $this->variants()
            ->where('item_id', $itemId)
            ->active()
            ->ordered();
    }
}
