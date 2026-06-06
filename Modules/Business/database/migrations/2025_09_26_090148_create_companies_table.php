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
        if (Schema::hasTable('companies')) {
            return;
        }

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // Company Name
            $table->string('code', 10)->nullable();          // Short Code (like OPR for OPECO Riyadh)

            $table->string('crn')->unique()->nullable();
            $table->string('trn')->unique()->nullable();

            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();

            // Defaults
            $table->string('currency')->default('SAR');

            //
            $table->unsignedBigInteger('hr_id')->nullable();
            $table->unsignedBigInteger('vp_id')->nullable();

            $table->boolean('is_group')->default(false);  // Parent company / group structure
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
