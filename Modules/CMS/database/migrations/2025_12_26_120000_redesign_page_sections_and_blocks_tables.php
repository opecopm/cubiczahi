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
        // Drop existing tables if they exist to ensure clean slate for redesign
        Schema::dropIfExists('cms_page_blocks');
        Schema::dropIfExists('cms_page_sections');

        Schema::create('cms_page_sections', function (Blueprint $table) {
            $table->id(); // bigint pk ai
            $table->unsignedBigInteger('page_id')->index(); // bigint indexed
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->json('items_list')->nullable();
            $table->string('icon_type')->nullable(); // 'library' or 'upload'
            $table->string('icon_class')->nullable(); // icon library class name
            $table->string('icon_image')->nullable(); // used by Spatie Media Library
            $table->string('background_color')->nullable();
            $table->string('column_width')->nullable();
            $table->json('buttons')->nullable();
            $table->integer('sort_order')->index()->default(0);
            $table->timestamps();
        });

        Schema::create('cms_page_blocks', function (Blueprint $table) {
            $table->id(); // bigint pk ai
            $table->unsignedBigInteger('page_section_id')->index(); // bigint indexed. Note: No foreign key constraint requested.
            $table->string('type')->default('default'); // type string. Using default 'default' to avoid null issues if not specified.
            $table->string('heading')->nullable();
            $table->string('subheading')->nullable();
            $table->text('description')->nullable();
            $table->json('items_list')->nullable();
            $table->string('icon_type')->nullable();
            $table->string('icon_class')->nullable();
            $table->string('icon_image')->nullable();
            $table->string('background_color')->nullable();
            $table->string('column_width')->nullable();
            $table->json('buttons')->nullable();
            $table->integer('sort_order')->index()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_blocks');
        Schema::dropIfExists('page_sections');
    }
};
