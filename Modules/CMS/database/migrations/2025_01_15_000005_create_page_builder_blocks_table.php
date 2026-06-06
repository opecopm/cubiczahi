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
        Schema::create('cms_page_builder_blocks', function (Blueprint $table) {
            $table->id();
            $table->integer('column_id');
            $table->string('block_type'); // text, image, button, video, spacer, divider, html
            $table->integer('sort_order')->default(0);
            $table->json('content')->nullable(); // Block-specific content data
            $table->json('settings')->nullable(); // styling, settings, etc.
            $table->json('css_classes')->nullable();
            $table->json('custom_css')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_page_builder_blocks');
    }
};


