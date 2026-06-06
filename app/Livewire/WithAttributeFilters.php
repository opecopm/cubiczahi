<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemAttribute;

trait WithAttributeFilters
{
    public array $attributeFilters = [];

    public function getAvailableAttributeFilters()
    {
        // Get distinct attribute slugs and their values from current items
        return ItemAttribute::query()
            ->join('items', 'item_attributes.item_id', '=', 'items.id')
            ->whereIn('items.id', $this->getCurrentItemIds())
            ->with(['values' => fn($q) => $q->select('attribute_id', 'value')])
            ->select('item_attributes.slug', 'item_attributes.name', 'item_attributes.type')
            ->whereNotNull('slug')
            ->distinct()
            ->get()
            ->map(function($attr) {
                return [
                    'slug' => $attr->slug,
                    'name' => $attr->name,
                    'type' => $attr->type,
                    'values' => $attr->values->pluck('value')->unique()->values()
                ];
            });
    }

    protected function getCurrentItemIds()
    {
        // Default implementation - components should override for better performance
        return Item::pluck('id')->toArray();
    }

    protected function applyAttributeFilters(Builder $query): Builder
    {
        foreach ($this->attributeFilters as $attrSlug => $attrValue) {
            if (empty($attrValue)) continue;

            $query->whereHas('attributes', function($attrQ) use ($attrSlug, $attrValue) {
                $attrQ->where('slug', $attrSlug)
                      ->whereHas('values', fn($valQ) => $valQ->where('value', $attrValue));
            });
        }

        return $query;
    }

    public function applyFilters(Builder $query, $model): Builder
    {
        $query = parent::applyFilters($query, $model);
        return $this->applyAttributeFilters($query);
    }
}
