<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('media_assets') || ! Schema::hasTable('media_folders')) {
            return;
        }

        Schema::table('media_assets', function (Blueprint $table) {
            if (! Schema::hasColumn('media_assets', 'folder_id')) {
                $table->foreignId('folder_id')
                    ->nullable()
                    ->after('company_id')
                    ->constrained('media_folders')
                    ->nullOnDelete();
            }
        });

        $this->backfillLegacyFolders();
    }

    public function down(): void
    {
        if (! Schema::hasTable('media_assets') || ! Schema::hasColumn('media_assets', 'folder_id')) {
            return;
        }

        Schema::table('media_assets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('folder_id');
        });
    }

    private function backfillLegacyFolders(): void
    {
        DB::table('media_assets')
            ->select(['id', 'company_id', 'folder'])
            ->whereNull('folder_id')
            ->whereNotNull('folder')
            ->orderBy('id')
            ->each(function (object $asset): void {
                $folderPath = trim(str_replace('\\', '/', (string) $asset->folder), '/');

                if ($folderPath === '') {
                    return;
                }

                $parentId = null;
                $currentPath = '';

                foreach (array_values(array_filter(explode('/', $folderPath))) as $segment) {
                    $segment = trim($segment);

                    if ($segment === '') {
                        continue;
                    }

                    $currentPath = ltrim($currentPath.'/'.$segment, '/');
                    $folderId = $this->findOrCreateFolder(
                        companyId: $asset->company_id,
                        parentId: $parentId,
                        name: $segment,
                        path: $currentPath
                    );

                    $parentId = $folderId;
                }

                if ($parentId) {
                    DB::table('media_assets')
                        ->where('id', $asset->id)
                        ->update(['folder_id' => $parentId]);
                }
            });
    }

    private function findOrCreateFolder(?int $companyId, ?int $parentId, string $name, string $path): int
    {
        $query = DB::table('media_folders')
            ->select('id')
            ->where('name', $name)
            ->where('path', $path);

        if ($companyId === null) {
            $query->whereNull('company_id');
        } else {
            $query->where('company_id', $companyId);
        }

        if ($parentId === null) {
            $query->whereNull('parent_id');
        } else {
            $query->where('parent_id', $parentId);
        }

        $existing = $query->first();

        if ($existing) {
            return (int) $existing->id;
        }

        return (int) DB::table('media_folders')->insertGetId([
            'company_id' => $companyId,
            'parent_id' => $parentId,
            'name' => $name,
            'slug' => Str::slug($name),
            'path' => $path,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
