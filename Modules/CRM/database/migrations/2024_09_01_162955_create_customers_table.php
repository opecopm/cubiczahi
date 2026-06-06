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
        if (Schema::hasTable('customers')) {
            return;
        }

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->integer('custom_group_id')->nullable();
            $table->string('reference', 255)->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->string('company')->nullable();
            $table->string('industry')->nullable();
            $table->string('website')->nullable();
            $table->string('crn')->nullable();
            $table->string('trn')->nullable();
            $table->string('status')->default('active')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Add soft deletes column
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
