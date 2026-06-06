<?php

namespace Modules\Inventory\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemPrice;

class ServicesImport implements ToCollection, WithChunkReading, WithHeadingRow
{
    public int $created = 0;

    public int $updated = 0;

    public int $skipped = 0;

    public array $errors = [];

    public function chunkSize(): int
    {
        return 200;
    }

    public function collection(Collection $rows): void
    {
        $this->importPrice($rows);
    }

    private function importPrice(Collection $rows): void
    {
        foreach ($rows as $i => $row) {
            $rowNum      = $i + 2;
            $reference   = trim((string) ($row['reference'] ?? ''));
            $nameEn      = trim((string) ($row['name_en'] ?? ''));
            $rawPrice    = $row['sell_price'] ?? null;
            $sellingPrice = is_numeric($rawPrice) ? (float) $rawPrice : null;

            if ($reference === '' || $nameEn === '') {
                $this->skipped++;
                continue;
            }

            $existing = Item::where('reference', $reference)->first();

            if ($existing && $existing->type !== 'service') {
                $this->errors[] = "Row {$rowNum}: item with reference '{$reference}' is type '{$existing->type}', expected 'service'.";
                $this->skipped++;
                continue;
            }

            if ($existing) {
                $existing->setTranslation('name', 'en', $nameEn);
                $existing->model_number = $reference;
                $existing->save();
                $this->updated++;
                $item = $existing;
            } else {
                $item = Item::create([
                    'reference' => $reference,
                    'type' => 'service',
                    'name' => ['en' => $nameEn],
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
}
