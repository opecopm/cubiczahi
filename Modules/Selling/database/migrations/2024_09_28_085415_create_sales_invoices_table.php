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
        if (Schema::hasTable('sales_invoices')) {
            return;
        }

        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->default(1); // Company ID
            $table->string('reference')->unique();
            $table->unsignedInteger('customer_id'); // Foreign key for customer
            $table->decimal('total_price', 15, 2);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->decimal('subtotal', 15, 2);
            $table->unsignedInteger('tax_id')->nullable(); // Foreign key for tax
            $table->decimal('tax', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->decimal('due_amount', 15, 2)->default(0.00);
            $table->string('status')->default('unpaid'); // Status: unpaid, paid, partially_paid, overdue, etc.
            $table->string('currency')->default('USD');
            $table->decimal('currency_rate', 15, 4)->default(1.0000);
            $table->timestamp('issued_at');
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedInteger('sales_order_id')->nullable();
            $table->unsignedInteger('purchase_order_id')->nullable();
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
        Schema::dropIfExists('sales_invoices');
    }
};
