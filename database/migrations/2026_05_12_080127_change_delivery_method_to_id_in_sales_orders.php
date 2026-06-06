<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('delivery_method');
            $table->foreignId('delivery_method_id')->nullable()->after('delivery_date')
                  ->constrained('delivery_methods')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_method_id']);
            $table->dropColumn('delivery_method_id');
            $table->string('delivery_method')->nullable()->after('delivery_date');
        });
    }
};
