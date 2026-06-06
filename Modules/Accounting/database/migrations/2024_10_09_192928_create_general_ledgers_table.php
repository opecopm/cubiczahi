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
        if (Schema::hasTable('general_ledgers')) {
            return;
        }
        Schema::create('general_ledgers', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('reference', 100);
            $table->text('description')->nullable();
            $table->integer('account_id');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->virtualAs('debit - credit');
            $table->integer('created_by');  // Who created the entry line
            $table->integer('updated_by')->nullable();  // Who last updated the line
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_ledgers');
    }
};
