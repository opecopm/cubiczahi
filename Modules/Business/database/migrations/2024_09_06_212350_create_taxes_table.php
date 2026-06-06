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
        if (Schema::hasTable('taxes')) {
            return;
        }

        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->string('name'); // e.g., VAT, GST
            $table->decimal('rate', 5, 2); // e.g., 10.00 for 10%
            $table->string('status')->default('active'); // Status of the tax rate
            $table->boolean('is_default')->default(false); // Indicates if this is the default tax rate
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
