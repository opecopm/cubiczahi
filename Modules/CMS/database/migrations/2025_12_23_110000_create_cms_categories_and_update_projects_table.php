<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 0. Cleanup from previous attempt error if exists
        if (Schema::hasTable('cms_categories')) {
            Schema::dropIfExists('cms_categories');
        }

        // 1. Create Project Categories Table
        if (!Schema::hasTable('cms_project_categories')) {
            Schema::create('cms_project_categories', function (Blueprint $table) {
                $table->id('category_id');
                $table->json('category_name'); // Translatable
                $table->json('slug')->nullable(); // Translatable
                $table->string('icon_class')->nullable();
                $table->string('icon_image')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Update Projects Table
        Schema::table('cms_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('cms_projects', 'category_id')) {
                $table->integer('category_id')->nullable()->after('project_id'); // No FK
            }
            if (!Schema::hasColumn('cms_projects', 'tags')) {
                $table->json('tags')->nullable()->after('status'); 
            }
            if (!Schema::hasColumn('cms_projects', 'additional_info')) {
                $table->json('additional_info')->nullable()->after('tags'); 
            }
        });
    }

    public function down(): void
    {
        Schema::table('cms_projects', function (Blueprint $table) {
            // Check before dropping to avoid errors if manually running down
            if (Schema::hasColumn('cms_projects', 'category_id')) $table->dropColumn('category_id');
            if (Schema::hasColumn('cms_projects', 'tags')) $table->dropColumn('tags');
            if (Schema::hasColumn('cms_projects', 'additional_info')) $table->dropColumn('additional_info');
        });

        Schema::dropIfExists('cms_project_categories');
    }
};
