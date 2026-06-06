<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('customers')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) {
            // Add the new customer_group_id column and FK
            $table->unsignedBigInteger('customer_group_id')->nullable()->after('id');
        });

        // Attempt to backfill from legacy custom_group_id if present
        try {
            DB::statement('UPDATE customers SET customer_group_id = custom_group_id WHERE customer_group_id IS NULL');
        } catch (Throwable $e) {
            // Silently ignore if custom_group_id does not exist or DB does not support the statement
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop FK then column
            $table->dropForeign(['customer_group_id']);
            $table->dropColumn('customer_group_id');
        });
    }
};
