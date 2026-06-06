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
        Schema::create('cms_meta_tags', function (Blueprint $table) {
            $table->id();
            $table->integer('page_id');
            $table->string('key');     // e.g. "robots", "theme-color", "fb:app_id"
            $table->json('value')->nullable();     // e.g. "noindex", "#ffffff", "1234567890"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_meta_tags');
    }
};
