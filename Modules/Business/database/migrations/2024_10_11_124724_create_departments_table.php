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
        if (Schema::hasTable('departments')) {
            return;
        }
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();  // Department code (optional)
            $table->string('name');  // Department name
            $table->text('description')->nullable();  // Department description (optional)
            $table->integer('hod_id')->nullable(); // connected to employees
            $table->integer('parent_id')->nullable();
            $table->string('status');
            // Auditing columns
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
