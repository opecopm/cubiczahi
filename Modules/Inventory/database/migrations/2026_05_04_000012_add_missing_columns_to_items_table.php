<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'is_serialized')) {
                $table->boolean('is_serialized')->default(false)->after('track_inventory');
            }
            if (! Schema::hasColumn('items', 'has_variants')) {
                $table->boolean('has_variants')->default(false)->after('is_serialized');
            }
            if (! Schema::hasColumn('items', 'warranty_months')) {
                $table->integer('warranty_months')->nullable()->after('brand_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            foreach (['is_serialized', 'has_variants', 'warranty_months'] as $col) {
                if (Schema::hasColumn('items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
