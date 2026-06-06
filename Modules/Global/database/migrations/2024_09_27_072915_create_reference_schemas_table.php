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
        if (Schema::hasTable('reference_schemas')) {
            return;
        }

        Schema::create('reference_schemas', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // e.g., 'sales_order', 'purchase_order', 'invoice'
            $table->string('model')->unique(); // e.g., 'Modules\Selling\Models\SalesOrder'
            $table->string('prefix')->nullable(); // e.g., 'SO-', 'PO-', 'INV-'
            $table->string('date_prefix')->default('Ymd')->nullable(); // e.g., '202409'
            $table->string('reset_period')->default('monthly')->nullable();
            $table->integer('initial_value')->default(1);
            $table->integer('increment')->default(1);
            $table->integer('next_value')->default(1);
            $table->integer('digits')->default(6);
            $table->string('status')->default('active'); // Status of the schema
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reference_schemas');
    }
};
