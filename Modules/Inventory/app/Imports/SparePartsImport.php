<?php

namespace Modules\Inventory\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Business\Models\Location;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemPrice;
use Modules\Inventory\Services\StockLedgerService;

class SparePartsImport implements ToCollection, WithChunkReading, WithHeadingRow
{
    public int $created = 0;

    public int $updated = 0;

    public int $skipped = 0;

    public array $errors = [];

    public function __construct(
        private readonly string $mode = 'price'
    ) {}

    public function chunkSize(): int
    {
        return 200;
    }

    public function collection(Collection $rows): void
    {
        if ($this->mode === 'quantity') {
            $this->importQuantity($rows);
        } else {
            $this->importPrice($rows);
        }
    }

    private function importPrice(Collection $rows): void
    {
        foreach ($rows as $row) {
            $reference = trim((string) ($row['reference'] ?? ''));
            $nameEn = trim((string) ($row['name_en'] ?? ''));
            $nameAr = trim((string) ($row['name_ar'] ?? ''));
            $rawPrice = $row['sell_price'] ?? null;
            $sellingPrice = is_numeric($rawPrice) ? (float) $rawPrice : null;

            if ($reference === '' || $nameEn === '') {
                $this->skipped++;

                continue;
            }

            $existing = Item::where('reference', $reference)->first();

            if ($existing) {
                $existing->setTranslation('name', 'en', $nameEn);
                if ($nameAr !== '') {
                    $existing->setTranslation('name', 'ar', $nameAr);
                }
                $existing->model_number = $reference;
                $existing->save();
                $this->updated++;
                $item = $existing;
            } else {
                $name = ['en' => $nameEn];
                if ($nameAr !== '') {
                    $name['ar'] = $nameAr;
                }

                $item = Item::create([
                    'reference' => $reference,
                    'type' => 'spare_part',
                    'name' => $name,
                    'model_number' => $reference,
                    'status' => 'active',
                ]);
                $this->created++;
            }

            if ($sellingPrice !== null) {
                ItemPrice::updateOrCreate(
                    ['item_id' => $item->id, 'price_type' => 'sell'],
                    ['price' => $sellingPrice, 'currency' => 'SAR', 'currency_rate' => 1, 'is_default' => true]
                );
            }
        }
    }

    private function importQuantity(Collection $rows): void
    {
        /** @var User $authUser */
        $authUser = Auth::user();
        $companyId = $authUser?->defaultCompany()?->id;
        if (! $companyId) {
            throw new \RuntimeException('No default company found for the authenticated user.');
        }

        $service = app(StockLedgerService::class);

        foreach ($rows as $i => $row) {
            $rowNum = $i + 2;
            $reference = trim((string) ($row['reference'] ?? ''));
            $locationCode = trim((string) ($row['location_code'] ?? ''));
            $rawQty = $row['qty'] ?? null;
            $qty = is_numeric($rawQty) ? (float) $rawQty : null;

            if ($reference === '' || $locationCode === '' || $qty === null) {
                $this->skipped++;

                continue;
            }

            $item = Item::where('reference', $reference)->first();
            if (! $item) {
                $this->errors[] = "Row {$rowNum}: item with reference '{$reference}' not found.";
                $this->skipped++;

                continue;
            }

            $location = Location::where('code', $locationCode)->first();
            if (! $location) {
                $this->errors[] = "Row {$rowNum}: location with code '{$locationCode}' not found.";
                $this->skipped++;

                continue;
            }

            try {
                $service->postTransaction([
                    'company_id' => $companyId,
                    'type' => 'adjustment',
                    'item_id' => $item->id,
                    'location_id' => $location->id,
                    'quantity' => $qty,
                    'occurred_at' => now(),
                    'created_by' => Auth::id(),
                ]);
                $this->updated++;
            } catch (\Throwable $e) {
                $this->errors[] = "Row {$rowNum} ({$reference}): ".$e->getMessage();
                $this->skipped++;
            }
        }
    }
}
