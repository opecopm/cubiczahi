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
        Schema::table('cms_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('cms_pages', 'subtitle')) {
                $table->string('subtitle')->nullable()->after('title');
            }
            if (!Schema::hasColumn('cms_pages', 'alternative_title')) {
                $table->string('alternative_title')->nullable()->after('title');
            }
            if (!Schema::hasColumn('cms_pages', 'breadcrumb_image')) {
                $table->string('breadcrumb_image')->nullable()->after('alternative_title');
            }
            if (!Schema::hasColumn('cms_pages', 'video_url')) {
                $table->string('video_url')->nullable()->after('breadcrumb_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->dropColumn(['subtitle', 'alternative_title', 'breadcrumb_image', 'video_url']);
        });
    }
};
