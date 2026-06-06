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
        if (Schema::hasTable('payments')) {
            return;
        }
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->date('payment_date');
            $table->string('reference', 100)->unique();
            $table->enum('payment_type', ['incoming', 'outgoing']);
            $table->decimal('amount', 15, 2);
            $table->unsignedInteger('account_id')->nullable();
            $table->string('trans_reference', 100)->nullable(); // link to invoice, bill, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
