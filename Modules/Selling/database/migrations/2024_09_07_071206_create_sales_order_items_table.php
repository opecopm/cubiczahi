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
        if (Schema::hasTable('sales_order_items')) {
            return;
        }

        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sales_order_id');
            $table->unsignedInteger('item_id');
            $table->text('name');
            $table->text('description');
            $table->string('unit');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->string('discount_type')->nullable();
            $table->decimal('discount_rate', 15, 2)->default(0.00);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->decimal('subtotal', 15, 2);
            $table->unsignedInteger('tax_id')->nullable(); // Foreign key for tax
            $table->string('tax_name')->nullable();
            $table->decimal('tax_rate', 15, 2)->default(0.00);
            $table->decimal('tax', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2);
            $table->boolean('is_rental')->default(0);
            $table->timestamp('rental_start_at')->nullable();
            $table->timestamp('rental_end_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_items');
    }
};
