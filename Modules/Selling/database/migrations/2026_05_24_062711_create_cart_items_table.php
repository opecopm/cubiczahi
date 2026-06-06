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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id')->index();
            $table->unsignedBigInteger('item_id')->index();
            $table->unsignedBigInteger('variant_id')->nullable()->index();
            $table->decimal('quantity', 15, 2);
            $table->string('unit')->default('pcs');
            $table->decimal('price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('coupon_code')->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('discount_rate', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->unsignedInteger('tax_id')->nullable(); // Foreign key for tax
            $table->string('tax_name')->nullable();
            $table->decimal('tax_rate', 10, 2)->default(0.00);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_rental')->default(false);
            $table->timestamp('rental_start_at')->nullable();
            $table->timestamp('rental_end_at')->nullable();
            $table->timestamps();

            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
