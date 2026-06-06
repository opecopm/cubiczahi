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
        if (Schema::hasTable('states')) {
            return;
        }

        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');  // Foreign key reference to 'countries' table
            $table->string('name');
            $table->string('state_code', 10); // State or province code, may vary in length
            $table->string('latitude')->nullable();  // Precision up to 8 decimal points
            $table->string('longitude')->nullable(); // Precision up to 8 decimal points
            $table->enum('status', ['active', 'inactive'])->default('active');  // Active/inactive status
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
