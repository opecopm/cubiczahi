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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable()->index();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->string('status')->default('active'); // active, abandoned, converted
            $table->decimal('total_price', 15, 2);
            $table->string('coupon_code')->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('discount_rate', 10, 2)->default(0.00);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->decimal('subtotal', 15, 2);
            $table->unsignedInteger('tax_id')->nullable(); // Foreign key for tax
            $table->decimal('tax', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2);
            $table->string('payment_status')->default('unpaid'); // Status: unpaid, paid, partially_paid, overdue, etc.
            $table->string('currency')->default('SAR');
            $table->decimal('currency_rate', 10, 4)->default(1.0000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
