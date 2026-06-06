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
        if (Schema::hasTable('item_prices')) {
            return;
        }

        Schema::create('item_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->decimal('price', 15, 2);
            $table->enum('price_type', ['selling', 'purchase']);
            $table->string('currency', 10);
            $table->decimal('currency_rate', 15, 6)->default(1.000000);

            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();

            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();

            $table->boolean('is_default')->default(0); // ðŸ‘ˆ added default flag

            $table->timestamps();

            $table->index(['item_id', 'price_type']);
            $table->index(['vendor_id']);
            $table->index(['customer_id']);
            $table->index(['is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_prices');
    }
};
