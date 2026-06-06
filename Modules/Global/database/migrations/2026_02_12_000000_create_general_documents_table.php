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
        if (Schema::hasTable('general_documents')) {
            return;
        }

        Schema::create('general_documents', function (Blueprint $table) {
            $table->id();

            // Polymorphic relationship fields
            // This allows connecting to Customers, Vendors, Employees, etc.
            $table->morphs('documentable');

            $table->string('name')->nullable(); // Document name/title
            $table->string('type')->nullable(); // CR, TRN, ID, etc.
            $table->string('document_number')->nullable(); // The ID number or CR number

            // Dates
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();

            // Additional details
            $table->string('issuing_country')->nullable();
            $table->string('issuing_entity')->nullable();

            $table->text('description')->nullable();
            $table->string('status')->default('active'); // active, expired, archived

            // Tracking
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_documents');
    }
};
