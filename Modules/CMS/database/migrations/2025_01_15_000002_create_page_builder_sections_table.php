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
        Schema::create('cms_page_builder_sections', function (Blueprint $table) {
            $table->id();
            $table->integer('page_id');
            $table->string('section_type')->default('section'); // section, container
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable(); // background, padding, margin, etc.
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
        Schema::dropIfExists('cms_page_builder_sections');
    }
};


