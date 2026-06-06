<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'is_asset')) {
                $table->dropColumn('is_asset');
            }
            if (Schema::hasColumn('items', 'asset_id')) {
                $table->dropColumn('asset_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('is_asset')->default(false)->after('model_number');
            $table->unsignedBigInteger('asset_id')->nullable()->after('is_asset');
        });
    }
};
