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
        if (Schema::hasTable('cities')) {
            return;
        }

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->constrained('states')->onDelete('cascade');  // Foreign key reference to 'states' table
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade'); // Foreign key reference to 'countries' table
            $table->string('name');
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
        Schema::dropIfExists('cities');
    }
};
