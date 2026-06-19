<?php

namespace Modules\Inventory\Imports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Modules\Business\Models\BusinessPartner;
use Modules\Business\Models\Department;
use Modules\Business\Models\Location;
use Modules\Global\Models\CustomField;
use Modules\Inventory\Models\Brand;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCategory;
use Modules\Inventory\Models\ItemPrice;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

use function system_setting;

class ItemsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected array $activeLanguages;

    protected array $categoryIdCache = [];

    protected array $brandIdCache = [];

    protected array $departmentIdCache = [];

    protected array $locationIdCache = [];

    protected array $sponsorIdCache = [];

    protected string $defaultBrandName = 'General';

    protected ?array $itemColumnListing = null;

    public function __construct()
    {
        $langs = system_setting('active_languages', ['ar']);
        $this->activeLanguages = is_string($langs) ? (json_decode($langs, true) ?? [$langs]) : $langs;
    }

    public function model(array $row)
    {
        $row = $this->normalizeRow($row);
        $this->validateRow($row);

        return DB::transaction(function () use ($row) {
            $category_id = $this->resolveCategoryId($row['category'] ?? null, $row['category_id'] ?? null);
            $brand_id = $this->resolveBrandId($row['brand'] ?? null, $row['brand_id'] ?? null);
            $sponsorId = $this->resolveSponsorId(
                $this->firstNonNullRowValue($row, ['sponsor', 'sponsor_name', 'sponsor_email']),
                $row['sponsor_id'] ?? null
            );

            $rowType = $this->isNullLike($row['type'] ?? null) ? null : strtolower(trim((string) ($row['type'] ?? '')));
            if ($rowType && $rowType !== 'product') {
                throw new \Exception("Only product type is supported in this import. Given: {$rowType}");
            }

            $type = 'product';
            $trackInventory = array_key_exists('track_inventory', $row)
                ? (bool) $row['track_inventory']
                : ($type === 'spare_part');

            $nameTranslation = ['en' => $row['name_en'] ?? ''];
            foreach ($this->activeLanguages as $lang) {
                if ($lang !== 'en') {
                    $nameTranslation[$lang] = $row["name_{$lang}"] ?? ($row['name_en'] ?? '');
                }
            }

            $itemAttributes = [
                'id' => $row['id'] ?? null,
                'reference' => $row['reference'] ?? null,
                'type' => $type,
                'name' => $nameTranslation,
                'category_id' => $category_id,
                'brand_id' => $brand_id,
                'model_number' => $row['model_number'] ?? null,
                'warranty_months' => $row['warranty_months'] ?? null,
                'track_inventory' => (bool) $trackInventory,
                'is_serialized' => (bool) ($row['is_serialized'] ?? false),
                'description' => $row['description'] ?? null,
                'status' => $row['status'] ?? 'active',
            ];

            if ($this->itemHasColumn('short_description')) {
                $itemAttributes['short_description'] = $row['short_description'] ?? null;
            }

            if ($this->itemHasColumn('part_id')) {
                $itemAttributes['part_id'] = $row['part_id'] ?? null;
            }

            if ($this->itemHasColumn('part_code')) {
                $itemAttributes['part_code'] = $row['part_code'] ?? null;
            }

            if ($this->itemHasColumn('sponsor_id')) {
                $itemAttributes['sponsor_id'] = $sponsorId;
            }

            if ($this->itemHasColumn('model_year')) {
                $itemAttributes['model_year'] = $row['model_year'] ?? null;
            }

            if ($this->itemHasColumn('serial_no')) {
                $itemAttributes['serial_no'] = $row['serial_number'] ?? null;
            }

            $item = Item::create($itemAttributes);

            $currency = $row['currency'] ?? ($row['currency_code'] ?? 'SAR');
            $currencyRate = $row['currency_rate'] ?? 1;

            ItemPrice::create([
                'item_id' => $item->id,
                'price' => $row['sell_price'] ?? ($row['selling_price'] ?? 0),
                'price_type' => 'sell',
                'currency' => $currency,
                'currency_rate' => $currencyRate,
            ]);

            ItemPrice::create([
                'item_id' => $item->id,
                'price' => $row['purchase_price'] ?? 0,
                'price_type' => 'purchase',
                'currency' => $currency,
                'currency_rate' => $currencyRate,
            ]);

            return $item;
        });
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable|integer|min:1',
            'type' => 'nullable|string|in:product',
            'category' => 'required_without:category_id|string|max:255',
            'category_id' => 'nullable|integer',
            'brand' => 'nullable|string|max:255',
            'brand_id' => 'nullable|integer',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'warranty_months' => 'nullable|integer|min:0',
            'track_inventory' => 'nullable|boolean',
            'is_serialized' => 'nullable|boolean',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive,pending',
            'sponsor' => 'nullable|string|max:255',
            'sponsor_name' => 'nullable|string|max:255',
            'sponsor_email' => 'nullable|string|max:255',
            'sponsor_id' => 'nullable|integer',
            'model_year' => 'nullable|integer',
            'serial_number' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:255',
            'sell_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'currency_code' => 'nullable|string|max:10',
            'currency_rate' => 'nullable|numeric|min:0',
            'part_id' => 'nullable|integer',
            'part_code' => 'nullable|string|max:255',
        ];
    }

    public function prepareForValidation($data, $index)
    {
        return $this->normalizeRow(is_array($data) ? $data : []);
    }

    protected function validateRow(array $row): void
    {
        $validator = Validator::make($row, $this->rules(), [], $this->customValidationAttributes());

        if ($validator->fails()) {
            throw new \Exception('Row validation failed: '.implode(', ', $validator->errors()->all()));
        }
    }

    public function customValidationAttributes(): array
    {
        $attributes = [
            'id' => 'ID',
            'name_en' => 'Name (EN)',
        ];
        foreach ($this->activeLanguages as $lang) {
            if ($lang !== 'en') {
                $attributes["name_{$lang}"] = 'Name ('.strtoupper($lang).')';
            }
        }
        return $attributes;
    }

    protected function normalizeRow(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            $normalizedKey = $this->normalizeHeadingKey($key);
            if ($normalizedKey === '') {
                continue;
            }

            if (! array_key_exists($normalizedKey, $normalized)) {
                $normalized[$normalizedKey] = $value;
            }
        }

        $aliases = [
            'name' => 'name_en',
            'item_name' => 'name_en',
            'item_name_en' => 'name_en',
            'sellingprice' => 'selling_price',
            'selling_price_sar' => 'selling_price',
            'sell_price' => 'sell_price',
            'purchaseprice' => 'purchase_price',
            'purchase_price_sar' => 'purchase_price',
            'currencycode' => 'currency_code',
            'currencyrate' => 'currency_rate',
            'asset_purchase_date' => 'purchase_date',
            'asset_purchase_cost' => 'purchase_cost',
        ];

        foreach ($aliases as $from => $to) {
            if (! array_key_exists($to, $normalized) && array_key_exists($from, $normalized)) {
                $normalized[$to] = $normalized[$from];
            }
        }

        foreach ($normalized as $key => $value) {
            $normalized[$key] = $this->normalizeValue($key, $value);
        }

        return $normalized;
    }

    protected function normalizeHeadingKey(string $key): string
    {
        $key = preg_replace('/^\xEF\xBB\xBF/', '', $key) ?? $key;
        $key = strtolower(trim($key));
        $key = preg_replace('/[^\pL\pN]+/u', '_', $key) ?? $key;

        return trim($key, '_');
    }

    protected function normalizeValue(string $key, mixed $value): mixed
    {
        if (is_string($value)) {
            $value = trim($value);
            if ($this->isNullLike($value)) {
                return null;
            }
        }

        $boolKeys = ['track_inventory', 'is_serialized'];
        if (in_array($key, $boolKeys, true)) {
            return $this->toBoolean($value);
        }

        $dateKeys = ['purchase_date', 'warranty_end_date', 'next_maintenance_date'];
        if (in_array($key, $dateKeys, true)) {
            return $this->toDateString($value);
        }

        $intKeys = [
            'id', 'category_id', 'brand_id', 'warranty_months', 'asset_category_id', 'department_id',
            'location_id', 'sponsor_id', 'model_year', 'useful_life_months',
            'po_id', 'product_id', 'part_id',
        ];
        if (in_array($key, $intKeys, true)) {
            return $this->toNullableInt($value);
        }

        $floatKeys = ['purchase_cost', 'value', 'sell_price', 'selling_price', 'purchase_price', 'currency_rate', 'salvage_value'];
        if (in_array($key, $floatKeys, true)) {
            return $this->toNullableFloat($value);
        }

        if ($key === 'type' && is_string($value)) {
            return strtolower(trim($value));
        }

        if ($key === 'status' && is_string($value)) {
            return strtolower(trim($value));
        }

        return $value;
    }

    protected function firstNonNullRowValue(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (! is_string($key)) {
                continue;
            }

            if (! array_key_exists($key, $row)) {
                continue;
            }

            $value = $row[$key];

            if ($this->isNullLike($value)) {
                continue;
            }

            return trim((string) $value);
        }

        return null;
    }

    protected function isNullLike(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value)) {
            $v = strtoupper(trim($value));

            return $v === '' || $v === 'NULL' || $v === 'N/A' || $v === 'NA' || $v === '-';
        }

        return false;
    }

    protected function toBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        if (! is_string($value)) {
            return false;
        }

        $v = strtolower(trim($value));

        return in_array($v, ['1', 'true', 'yes', 'y', 'on'], true);
    }

    protected function toNullableInt(mixed $value): ?int
    {
        if ($this->isNullLike($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        if (is_string($value) && preg_match('/^-?\d+$/', trim($value))) {
            return (int) trim($value);
        }

        return null;
    }

    protected function toNullableFloat(mixed $value): ?float
    {
        if ($this->isNullLike($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $v = preg_replace('/[^\d\.\-]+/', '', $value) ?? $value;
            if (is_numeric($v)) {
                return (float) $v;
            }
        }

        return null;
    }

    protected function toDateString(mixed $value): ?string
    {
        if ($this->isNullLike($value)) {
            return null;
        }

        if (is_numeric($value)) {
            try {
                $dt = ExcelDate::excelToDateTimeObject((float) $value);

                return $dt->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        try {
            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function resolveCategoryId(?string $categoryName, ?int $categoryId): ?int
    {
        if ($categoryId) {
            return $categoryId;
        }

        $name = $this->isNullLike($categoryName) ? null : trim((string) $categoryName);
        if (! $name) {
            return null;
        }

        if (array_key_exists($name, $this->categoryIdCache)) {
            return $this->categoryIdCache[$name];
        }

        $category = ItemCategory::firstOrCreate(['name' => $name]);
        $this->categoryIdCache[$name] = $category->id;

        return $category->id;
    }

    protected function resolveBrandId(?string $brandName, ?int $brandId): int
    {
        if ($brandId) {
            return $brandId;
        }

        $name = $this->isNullLike($brandName) ? null : trim((string) $brandName);
        if (! $name) {
            $name = $this->defaultBrandName;
        }

        if (array_key_exists($name, $this->brandIdCache)) {
            return $this->brandIdCache[$name];
        }

        $brand = Brand::firstOrCreate(['name' => $name]);
        $this->brandIdCache[$name] = $brand->id;

        return $brand->id;
    }

    protected function resolveProduct(?int $productId, ?string $productReference): ?Item
    {
        if ($productId) {
            return Item::query()->where('type', 'product')->whereKey($productId)->first();
        }

        if (! $this->isNullLike($productReference)) {
            return Item::query()->where('type', 'product')->where('reference', trim((string) $productReference))->first();
        }

        return null;
    }

    protected function resolveDepartmentId(?string $departmentName, ?int $departmentId): ?int
    {
        if ($departmentId) {
            return $departmentId;
        }

        $name = $this->isNullLike($departmentName) ? null : trim((string) $departmentName);
        if (! $name) {
            return null;
        }

        if (array_key_exists($name, $this->departmentIdCache)) {
            return $this->departmentIdCache[$name];
        }

        $id = Department::query()->where('name', $name)->value('id');
        if (! $id) {
            throw new \Exception("Unknown department: {$name}");
        }

        $this->departmentIdCache[$name] = (int) $id;

        return (int) $id;
    }

    protected function resolveLocationId(?string $locationName, ?int $locationId): ?int
    {
        if ($locationId) {
            return $locationId;
        }

        $name = $this->isNullLike($locationName) ? null : trim((string) $locationName);
        if (! $name) {
            return null;
        }

        if (array_key_exists($name, $this->locationIdCache)) {
            return $this->locationIdCache[$name];
        }

        $id = Location::query()->where('name', $name)->value('id');
        if (! $id) {
            throw new \Exception("Unknown location: {$name}");
        }

        $this->locationIdCache[$name] = (int) $id;

        return (int) $id;
    }

    protected function resolveSponsorId(?string $sponsorValue, ?int $sponsorId): ?int
    {
        if ($sponsorId) {
            return $sponsorId;
        }

        $value = $this->isNullLike($sponsorValue) ? null : trim((string) $sponsorValue);
        if (! $value) {
            return null;
        }

        if (array_key_exists($value, $this->sponsorIdCache)) {
            return $this->sponsorIdCache[$value];
        }

        $query = BusinessPartner::query()->select('id');

        if (str_contains($value, '@')) {
            $query->where('email', $value);
        } else {
            $query->where('name', $value);
        }

        $ids = $query->limit(2)->pluck('id');
        if ($ids->count() === 0) {
            throw new \Exception("Unknown sponsor: {$value}");
        }

        if ($ids->count() > 1) {
            throw new \Exception("Ambiguous sponsor: {$value}");
        }

        $id = (int) $ids->first();
        $this->sponsorIdCache[$value] = $id;

        return $id;
    }

    protected function itemHasColumn(string $column): bool
    {
        if ($this->itemColumnListing === null) {
            $this->itemColumnListing = Schema::hasTable('items') ? Schema::getColumnListing('items') : [];
        }

        return in_array($column, $this->itemColumnListing, true);
    }
}
