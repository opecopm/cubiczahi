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
        Schema::create('general_documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('related_to');
            $table->integer('related_id');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->timestamp('reminded_at')->nullable();
            $table->string('issuing_country')->nullable();
            $table->string('issuing_entity')->nullable();
            $table->string('reminded_email')->nullable();
            $table->string('file')->nullable();
            $table->string('file_type')->nullable();
            $table->string('link')->nullable();
            $table->string('status')->default('draft');
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
        Schema::dropIfExists('general_documents');
    }
};
