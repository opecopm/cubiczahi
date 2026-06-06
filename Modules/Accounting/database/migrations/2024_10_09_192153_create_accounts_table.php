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
        if (Schema::hasTable('accounts')) {
            return;
        }
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20); // Account code (e.g., 1001)
            $table->string('name'); // Account name (e.g., Cash, Accounts Receivable)
            $table->string('type')->default('asset');
            $table->text('description')->nullable(); // Account description
            $table->string('currency', 3)->default('SAR'); // Currency for the account
            $table->unsignedBigInteger('parent_id')->nullable(); // Parent account ID for sub-accounts
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('accounts');
    }
};
