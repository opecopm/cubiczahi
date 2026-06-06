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
        if (Schema::hasTable('countries')) {
            return;
        }

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iso3', 3);  // ISO 3166-1 alpha-3 code
            $table->string('iso2', 2);  // ISO 3166-1 alpha-2 code
            $table->string('phone_code');
            $table->string('currency');
            $table->string('currency_symbol')->nullable();  // Make nullable if some countries don't have symbols
            $table->string('region')->nullable();           // Optional based on geographic regions
            $table->string('latitude')->nullable();  // Precision up to 8 decimal points
            $table->string('longitude')->nullable(); // Precision up to 8 decimal points
            $table->enum('status', ['active', 'inactive'])->default('active');  // Active/inactive status
            $table->timestamps();  // Adds created_at and updated_at
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
