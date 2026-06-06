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
        if (Schema::hasTable('sales_orders')) {
            return;
        }

        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->default(1); // Company ID
            $table->string('reference')->unique(); // Serial number
            $table->unsignedInteger('customer_id');
            $table->decimal('total_price', 15, 2);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->decimal('subtotal', 15, 2);
            $table->unsignedInteger('tax_id')->nullable(); // Foreign key for tax
            $table->decimal('tax', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('due_amount', 15, 2)->default(0);
            $table->string('status')->default('Pending'); // Pending, Completed, Canceled
            $table->date('order_date');
            $table->date('delivery_date')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('currency_rate', 10, 2)->default(1);
            $table->unsignedInteger('created_by');  // Who created the entry line
            $table->unsignedInteger('updated_by')->nullable();  // Who last updated the line
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
