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
        if (Schema::hasTable('account_payables')) {
            return;
        }
        Schema::create('account_payables', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('vendor_id');
            $table->string('bill_number', 100)->unique();
            $table->date('bill_date');
            $table->date('due_date');
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('unpaid');
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
        Schema::dropIfExists('account_payables');
    }
};
