<?php

namespace Modules\Inventory\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemBalance;

use function system_setting;

class ItemsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    private string $mode;

    private ?string $type;

    private ?string $search;

    private array $filters;

    private string $secondLang;

    public function __construct(
        string $mode = 'list',
        ?string $type = null,
        ?string $search = null,
        array $filters = []
    ) {
        $this->mode = $mode;
        $this->type = $type;
        $this->search = $search;
        $this->filters = $filters;
        $this->secondLang = (string) system_setting('secondary_language', 'ar');
    }

    public function query()
    {
        return match ($this->mode) {
            'detailed' => $this->detailedQuery(),
            'prices' => $this->pricesQuery(),
            'quantities' => $this->quantitiesQuery(),
            default => $this->listQuery(),
        };
    }

    public function headings(): array
    {
        return match ($this->mode) {
            'detailed' => $this->detailedHeadings(),
            'prices' => $this->pricesHeadings(),
            'quantities' => ['reference', 'location_code', 'qty'],
            default => ['id', 'reference', 'category_en', 'name_en', 'selling_price', 'available', 'status'],
        };
    }

    public function map($row): array
    {
        return match ($this->mode) {
            'detailed' => $this->mapDetailed($row),
            'prices' => $this->mapPrices($row),
            'quantities' => $this->mapQuantities($row),
            default => $this->mapList($row),
        };
    }

    private function listQuery(): Builder
    {
        $queryBase = Item::query()->with(['category']);

        if ($this->type) {
            $queryBase->where('type', $this->type);
        }

        $query = $queryBase->clone();
        $query = $this->applySearch($query);
        $query = $this->applyFilters($query, new Item);

        $branchLocationIds = ItemBalance::query()
            ->whereHas('location', fn ($q) => $q->where('name', 'not like', '%local%purchase%'))
            ->distinct()
            ->pluck('location_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $sellPriceSubquery = DB::table('item_prices')
            ->select('price')
            ->whereColumn('item_id', 'items.id')
            ->where('price_type', 'sell')
            ->orderByDesc('is_default')
            ->orderByDesc('date_from')
            ->limit(1);

        return $query->select('items.*')
            ->selectSub($sellPriceSubquery, 'sell_price')
            ->withSum(['balances as branch_on_hand' => fn ($q) => $q->whereIn('location_id', $branchLocationIds)], 'on_hand')
            ->orderBy('items.id');
    }

    private function mapList($item): array
    {
        return [
            (int) $item->id,
            (string) ($item->reference ?? ''),
            (string) ($item->category?->name ?? ''),
            (string) $item->getTranslation('name', 'en'),
            $item->sell_price !== null ? (float) $item->sell_price : null,
            (float) ($item->branch_on_hand ?? 0),
            (string) ($item->status_label ?? 'NA'),
        ];
    }

    private function detailedQuery(): Builder
    {
        $queryBase = Item::query()
            ->with([
                'category',
                'brand',
            ]);

        if ($this->type) {
            $queryBase->where('type', $this->type);
        }

        $query = $queryBase->clone();
        $query = $this->applySearch($query);
        $query = $this->applyFilters($query, new Item);

        $sellPriceSubquery = DB::table('item_prices')
            ->select('price')
            ->whereColumn('item_id', 'items.id')
            ->where('price_type', 'sell')
            ->orderByDesc('is_default')
            ->orderByDesc('date_from')
            ->limit(1);

        $purchasePriceSubquery = DB::table('item_prices')
            ->select('price')
            ->whereColumn('item_id', 'items.id')
            ->where('price_type', 'purchase')
            ->orderByDesc('is_default')
            ->orderByDesc('date_from')
            ->limit(1);

        $currencySubquery = DB::table('item_prices')
            ->select('currency')
            ->whereColumn('item_id', 'items.id')
            ->where('price_type', 'sell')
            ->orderByDesc('is_default')
            ->orderByDesc('date_from')
            ->limit(1);

        $currencyRateSubquery = DB::table('item_prices')
            ->select('currency_rate')
            ->whereColumn('item_id', 'items.id')
            ->where('price_type', 'sell')
            ->orderByDesc('is_default')
            ->orderByDesc('date_from')
            ->limit(1);

        return $query->select('items.*')
            ->selectSub($sellPriceSubquery, 'sell_price')
            ->selectSub($purchasePriceSubquery, 'purchase_price')
            ->selectSub($currencySubquery, 'currency')
            ->selectSub($currencyRateSubquery, 'currency_rate')
            ->orderBy('items.id');
    }

    private function detailedHeadings(): array
    {
        return [
            'id',
            'type',
            'reference',
            'category',
            'category_id',
            'brand',
            'brand_id',
            'name_en',
            'name_ar',
            'model_number',
            'warranty_months',
            'track_inventory',
            'is_serialized',
            'short_description',
            'description',
            'status',
            'sponsor',
            'sponsor_name',
            'sponsor_email',
            'sponsor_id',
            'model_year',
            'serial_number',
            'plate_number',
            'sell_price',
            'selling_price',
            'purchase_price',
            'currency',
            'currency_code',
            'currency_rate',
            'part_id',
            'part_code',
        ];
    }

    private function mapDetailed($item): array
    {
        $serialNumber = (string) ($item->serial_no ?? '');
        $currency = (string) ($item->currency ?? 'SAR');

        return [
            $item->id !== null ? (int) $item->id : null,
            (string) ($item->type ?? ''),
            (string) ($item->reference ?? ''),
            (string) ($item->category?->name ?? ''),
            $item->category_id !== null ? (int) $item->category_id : null,
            (string) ($item->brand?->name ?? ''),
            $item->brand_id !== null ? (int) $item->brand_id : null,
            (string) $item->getTranslation('name', 'en'),
            (string) $item->getTranslation('name', 'ar'),
            (string) ($item->model_number ?? ''),
            $item->warranty_months !== null ? (int) $item->warranty_months : null,
            (int) (bool) $item->track_inventory,
            (int) (bool) $item->is_serialized,
            (string) ($item->short_description ?? ''),
            (string) ($item->description ?? ''),
            (string) ($item->status ?? ''),
            '',
            '',
            '',
            $item->sponsor_id !== null ? (int) $item->sponsor_id : null,
            $item->model_year !== null ? (int) $item->model_year : null,
            $serialNumber,
            '',
            $item->sell_price !== null ? (float) $item->sell_price : null,
            $item->sell_price !== null ? (float) $item->sell_price : null,
            $item->purchase_price !== null ? (float) $item->purchase_price : null,
            $currency,
            $currency,
            $item->currency_rate !== null ? (float) $item->currency_rate : 1,
            $item->part_id !== null ? (int) $item->part_id : null,
            (string) ($item->part_code ?? ''),
        ];
    }

    private function pricesQuery(): Builder
    {
        $queryBase = Item::query();

        if ($this->type) {
            $queryBase->where('type', $this->type);
        }

        $query = $queryBase->clone();
        $query = $this->applySearch($query);
        $query = $this->applyFilters($query, new Item);

        $sellPriceSubquery = DB::table('item_prices')
            ->select('price')
            ->whereColumn('item_id', 'items.id')
            ->where('price_type', 'sell')
            ->orderByDesc('is_default')
            ->orderByDesc('date_from')
            ->limit(1);

        return $query->select('items.*')
            ->selectSub($sellPriceSubquery, 'sell_price')
            ->orderBy('items.id');
    }

    private function pricesHeadings(): array
    {
        if ($this->type === 'service') {
            return ['reference', 'name_en', 'sell_price'];
        }

        return ['reference', 'name_en', 'name_ar', 'sell_price'];
    }

    private function mapPrices($item): array
    {
        if ($this->type === 'service') {
            return [
                (string) ($item->reference ?? ''),
                (string) $item->getTranslation('name', 'en'),
                $item->sell_price !== null ? (float) $item->sell_price : null,
            ];
        }

        return [
            (string) ($item->reference ?? ''),
            (string) $item->getTranslation('name', 'en'),
            (string) $item->getTranslation('name', 'ar'),
            $item->sell_price !== null ? (float) $item->sell_price : null,
        ];
    }

    private function quantitiesQuery(): Builder
    {
        $itemsQueryBase = Item::query();

        if ($this->type) {
            $itemsQueryBase->where('type', $this->type);
        }

        $itemsQuery = $itemsQueryBase->clone();
        $itemsQuery = $this->applySearch($itemsQuery);
        $itemsQuery = $this->applyFilters($itemsQuery, new Item);

        $itemIdsSubquery = $itemsQuery->select('items.id');

        return ItemBalance::query()
            ->with(['item', 'location'])
            ->whereIn('item_id', $itemIdsSubquery);
    }

    private function mapQuantities($balance): array
    {
        return [
            (string) ($balance->item?->reference ?? ''),
            (string) ($balance->location?->code ?? ''),
            $balance->on_hand !== null ? (float) $balance->on_hand : null,
        ];
    }

    private function applySearch(Builder $query): Builder
    {
        if (! filled($this->search)) {
            return $query;
        }

        $search = trim((string) $this->search);
        $secondLang = $this->secondLang;

        return $query->where(function ($q) use ($search, $secondLang) {
            $q->where('reference', 'like', '%'.$search.'%')
                ->orWhere('model_number', 'like', '%'.$search.'%')
                ->orWhere('name', 'like', '%'.$search.'%')
                ->orWhere('name->en', 'like', '%'.$search.'%')
                ->orWhere('name->'.$secondLang, 'like', '%'.$search.'%');
        });
    }

    private function applyFilters(Builder $query, Item $model): Builder
    {
        foreach ($model->filterable as $field => $meta) {
            $value = $this->filters[$field] ?? null;
            if ($value === '' || $value === null) {
                continue;
            }

            $operator = $meta['operator'] ?? '=';

            if (isset($meta['relation'], $meta['column'])) {
                $relation = (string) $meta['relation'];
                $column = (string) $meta['column'];

                $query->whereHas($relation, function ($q) use ($column, $operator, $value) {
                    if ($operator === 'like') {
                        $q->where($column, 'like', "%{$value}%");
                    } else {
                        $q->where($column, $operator, $value);
                    }
                });
            } else {
                if ($operator === 'like') {
                    $query->where($field, 'like', "%{$value}%");
                } else {
                    $query->where($field, $operator, $value);
                }
            }
        }

        return $query;
    }
}
