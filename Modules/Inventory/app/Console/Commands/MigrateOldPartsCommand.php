<?php

namespace Modules\Inventory\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Inventory\Models\Brand;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCategory;
use Modules\Inventory\Models\ItemPrice;

class MigrateOldPartsCommand extends Command
{
    protected $signature = 'inventory:migrate-old-parts
        {--old-connection=meet_services_old}
        {--old-host=127.0.0.1}
        {--old-port=3306}
        {--old-database=meet_services_old}
        {--old-username=root}
        {--old-password=}
        {--chunk=1000}
        {--include-deleted-parts}
        {--dry-run}
        {--force}';

    protected $description = 'Migrate parts and part-products relationships from an old database into items and items_to_items.';

    public function handle(): int
    {
        if (! $this->option('force') && ! app()->isLocal()) {
            $this->error('Refusing to run outside local environment without --force.');

            return self::FAILURE;
        }

        $oldConnection = (string) $this->option('old-connection');
        $this->ensureOldConnection($oldConnection);

        if (! Schema::connection($oldConnection)->hasTable('parts')) {
            $this->error("Old table not found: {$oldConnection}.parts");

            return self::FAILURE;
        }

        $pivotTable = null;
        if (Schema::connection($oldConnection)->hasTable('part_products')) {
            $pivotTable = 'part_products';
        } elseif (Schema::connection($oldConnection)->hasTable('part_product')) {
            $pivotTable = 'part_product';
        }

        if (! $pivotTable) {
            $this->error("Old table not found: {$oldConnection}.part_products (or {$oldConnection}.part_product)");

            return self::FAILURE;
        }

        $chunk = max(1, (int) $this->option('chunk'));
        $dryRun = (bool) $this->option('dry-run');
        $includeDeletedParts = (bool) $this->option('include-deleted-parts');

        $now = Carbon::now();

        $systemUserId = DB::table('users')->min('id') ?? 1;

        $hasPartIdColumn = Schema::hasColumn('items', 'part_id');
        $hasPartCodeColumn = Schema::hasColumn('items', 'part_code');

        $defaultBrandId = (int) Brand::query()->firstOrCreate(['name' => 'General'])->id;

        $categoryIdByOldId = $this->buildOldPartCategoryMap($oldConnection);

        $existingPartItemsByOldPartId = [];
        if ($hasPartIdColumn) {
            $existingPartItemsByOldPartId = DB::table('items')
                ->whereNotNull('part_id')
                ->pluck('id', 'part_id')
                ->mapWithKeys(function ($id, $partId) {
                    return [(int) $partId => (int) $id];
                })
                ->toArray();
        }

        $createdCount = 0;
        $skippedCount = 0;
        $updatedCount = 0;
        $idConflictCount = 0;

        $this->info('Migrating parts into items...');

        $partIdToNewItemId = $existingPartItemsByOldPartId;

        $partsQuery = DB::connection($oldConnection)->table('parts');
        if (! $includeDeletedParts && Schema::connection($oldConnection)->hasColumn('parts', 'deleted_at')) {
            $partsQuery->whereNull('deleted_at');
        }

        $partsQuery
            ->orderBy('id')
            ->chunkById($chunk, function ($rows) use (
                &$createdCount,
                &$skippedCount,
                &$updatedCount,
                &$idConflictCount,
                &$partIdToNewItemId,
                $categoryIdByOldId,
                $defaultBrandId,
                $dryRun,
                $hasPartCodeColumn,
                $hasPartIdColumn,
                $systemUserId,
                $now
            ) {
                foreach ($rows as $part) {
                    $oldPartId = (int) $part->id;

                    if (array_key_exists($oldPartId, $partIdToNewItemId)) {
                        $skippedCount++;

                        continue;
                    }

                    $candidateNewId = $oldPartId;
                    $idIsFree = Item::query()->whereKey($candidateNewId)->doesntExist();
                    if (! $idIsFree) {
                        $idConflictCount++;
                    }

                    $name = is_string($part->name) && trim($part->name) !== '' ? trim($part->name) : (string) $part->code;
                    $reference = is_string($part->code) && trim($part->code) !== '' ? trim($part->code) : "PART-{$oldPartId}";

                    $payload = [
                        'type' => 'spare_part',
                        'reference' => $reference,
                        'name' => ['en' => $name],
                        'description' => null,
                        'category_id' => $categoryIdByOldId[(int) ($part->part_category_id ?? 0)] ?? null,
                        'brand_id' => $defaultBrandId,
                        'model_number' => $reference,
                        'warranty_months' => null,
                        'track_inventory' => true,
                        'is_serialized' => false,
                        'status' => 'active',
                        'created_by' => $systemUserId,
                        'updated_by' => $systemUserId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    if ($idIsFree) {
                        $payload['id'] = $candidateNewId;
                    }

                    if ($hasPartIdColumn) {
                        $payload['part_id'] = $oldPartId;
                    }

                    if ($hasPartCodeColumn) {
                        $payload['part_code'] = $reference;
                    }

                    if ($dryRun) {
                        $createdCount++;
                        $partIdToNewItemId[$oldPartId] = $idIsFree ? $candidateNewId : (-1 * $oldPartId);

                        continue;
                    }

                    $item = Item::withoutEvents(function () use ($payload) {
                        return Item::create($payload);
                    });

                    $createdCount++;
                    $partIdToNewItemId[$oldPartId] = (int) $item->id;

                    if (isset($part->price) && is_numeric($part->price)) {
                        ItemPrice::query()->updateOrCreate(
                            ['item_id' => $item->id, 'price_type' => 'purchase'],
                            ['price' => (float) $part->price, 'currency' => 'SAR', 'currency_rate' => 1]
                        );
                    }
                }
            }, 'id');

        $this->info("Parts done. Created: {$createdCount}. Skipped(existing): {$skippedCount}. Updated: {$updatedCount}. ID conflicts: {$idConflictCount}.");

        $this->info("Migrating {$pivotTable} into items_to_items (product -> spare_part)...");

        $insertedPivots = 0;
        $skippedPivots = 0;
        $missingProduct = 0;
        $missingPart = 0;
        $missingProductSamples = [];
        $missingPartSamples = [];

        DB::connection($oldConnection)->table($pivotTable)
            ->orderBy('part_id')
            ->chunk($chunk, function ($rows) use (
                &$insertedPivots,
                &$skippedPivots,
                &$missingProduct,
                &$missingPart,
                &$missingProductSamples,
                &$missingPartSamples,
                $dryRun,
                $hasPartIdColumn,
                $partIdToNewItemId,
                $now
            ) {
                $pairs = [];

                foreach ($rows as $row) {
                    $oldPartId = (int) $row->part_id;
                    $oldProductId = (int) $row->product_id;

                    $productId = Item::query()
                        ->whereKey($oldProductId)
                        ->where('type', 'product')
                        ->value('id');

                    if (! $productId) {
                        $missingProduct++;
                        if (count($missingProductSamples) < 10) {
                            $missingProductSamples[$oldProductId] = true;
                        }

                        continue;
                    }

                    $partItemId = $partIdToNewItemId[$oldPartId] ?? null;
                    if (! $partItemId && $hasPartIdColumn) {
                        $partItemId = (int) DB::table('items')->where('part_id', $oldPartId)->value('id');
                    }

                    if (! $partItemId) {
                        $missingPart++;
                        if (count($missingPartSamples) < 10) {
                            $missingPartSamples[$oldPartId] = true;
                        }

                        continue;
                    }

                    $key = $productId.':'.$partItemId;
                    $pairs[$key] = [
                        'item_id' => (int) $productId,
                        'item_pivot_id' => (int) $partItemId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if ($pairs === []) {
                    return;
                }

                $candidateRows = array_values($pairs);
                $productIds = array_values(array_unique(array_map(fn ($r) => $r['item_id'], $candidateRows)));
                $partItemIds = array_values(array_unique(array_map(fn ($r) => $r['item_pivot_id'], $candidateRows)));

                $existing = DB::table('items_to_items')
                    ->whereIn('item_id', $productIds)
                    ->whereIn('item_pivot_id', $partItemIds)
                    ->get(['item_id', 'item_pivot_id'])
                    ->mapWithKeys(fn ($r) => [(int) $r->item_id.':'.(int) $r->item_pivot_id => true])
                    ->toArray();

                $toInsert = [];
                foreach ($candidateRows as $r) {
                    $k = $r['item_id'].':'.$r['item_pivot_id'];
                    if (isset($existing[$k])) {
                        $skippedPivots++;

                        continue;
                    }
                    $toInsert[] = $r;
                }

                if ($toInsert === []) {
                    return;
                }

                if ($dryRun) {
                    $insertedPivots += count($toInsert);

                    return;
                }

                DB::table('items_to_items')->insert($toInsert);
                $insertedPivots += count($toInsert);
            });

        $this->info("Pivot done. Inserted: {$insertedPivots}. Skipped(existing): {$skippedPivots}. Missing product: {$missingProduct}. Missing part: {$missingPart}.");
        if ($missingProductSamples !== []) {
            $this->warn('Missing product_id samples: '.implode(', ', array_map('strval', array_keys($missingProductSamples))));
        }
        if ($missingPartSamples !== []) {
            $this->warn('Missing part_id samples: '.implode(', ', array_map('strval', array_keys($missingPartSamples))));
        }

        if ($dryRun) {
            $this->warn('Dry-run mode enabled: no database writes were performed.');
        }

        return self::SUCCESS;
    }

    protected function ensureOldConnection(string $connectionName): void
    {
        if (config("database.connections.{$connectionName}") !== null) {
            return;
        }

        config([
            "database.connections.{$connectionName}" => [
                'driver' => 'mysql',
                'host' => (string) $this->option('old-host'),
                'port' => (string) $this->option('old-port'),
                'database' => (string) $this->option('old-database'),
                'username' => (string) $this->option('old-username'),
                'password' => (string) $this->option('old-password'),
                'unix_socket' => '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
                'options' => extension_loaded('pdo_mysql') ? array_filter([]) : [],
            ],
        ]);
    }

    protected function buildOldPartCategoryMap(string $oldConnection): array
    {
        if (! Schema::connection($oldConnection)->hasTable('part_categories')) {
            return [];
        }

        $map = [];
        $rows = DB::connection($oldConnection)->table('part_categories')->get(['id', 'name']);

        foreach ($rows as $row) {
            $name = is_string($row->name) ? trim($row->name) : '';
            if ($name === '') {
                continue;
            }

            $category = ItemCategory::query()->firstOrCreate(['name' => $name]);
            $map[(int) $row->id] = (int) $category->id;
        }

        return $map;
    }
}
