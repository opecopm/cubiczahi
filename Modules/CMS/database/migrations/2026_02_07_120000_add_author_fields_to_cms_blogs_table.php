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
        Schema::table('cms_blogs', function (Blueprint $table) {
            $table->string('author_name')->nullable()->after('featured_image');
            $table->string('author_image')->nullable()->after('author_name');
            $table->timestamp('published_at')->nullable()->after('status');
            $table->unsignedInteger('comments_count')->default(0)->after('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_blogs', function (Blueprint $table) {
            $table->dropColumn(['author_name', 'author_image', 'published_at', 'comments_count']);
        });
    }
};
