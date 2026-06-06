<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convert all Spatie translatable fields to proper json columns.
     * Also add missing columns (subtitle, alternative_title, breadcrumb_image, video_url, icon, page_type, is_featured)
     * that exist in the model's $fillable but not the original migration.
     */
    public function up(): void
    {
        // ── Step 1: Migrate existing string/text data to JSON wrappers ──────────
        // For any row that has a plain string value (not valid JSON), wrap it as {"en": value}
        $translatableCols = [
            'meta_description',
            'meta_keywords',
            'og_title',
            'og_description',
            'twitter_title',
            'twitter_description',
        ];

        foreach ($translatableCols as $col) {
            if (Schema::hasColumn('cms_pages', $col)) {
                DB::table('cms_pages')->get()->each(function ($row) use ($col) {
                    $raw = $row->$col;
                    if ($raw === null) return;
                    $decoded = json_decode($raw, true);
                    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                        // It's plain text — wrap it as English default
                        DB::table('cms_pages')->where('id', $row->id)->update([
                            $col => json_encode(['en' => $raw]),
                        ]);
                    }
                });
            }
        }

        // ── Step 2: Alter columns to json type ───────────────────────────────────
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->json('meta_description')->nullable()->change();
            $table->json('meta_keywords')->nullable()->change();
            $table->json('og_title')->nullable()->change();
            $table->json('og_description')->nullable()->change();
            $table->json('twitter_title')->nullable()->change();
            $table->json('twitter_description')->nullable()->change();
        });

        // ── Step 3: Add missing columns if they don't exist ─────────────────────
        Schema::table('cms_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('cms_pages', 'subtitle')) {
                $table->json('subtitle')->nullable();
            }
            if (!Schema::hasColumn('cms_pages', 'alternative_title')) {
                $table->json('alternative_title')->nullable();
            }
            if (!Schema::hasColumn('cms_pages', 'breadcrumb_image')) {
                $table->string('breadcrumb_image')->nullable();
            }
            if (!Schema::hasColumn('cms_pages', 'video_url')) {
                $table->string('video_url')->nullable();
            }
            if (!Schema::hasColumn('cms_pages', 'icon')) {
                $table->string('icon')->nullable();
            }
            if (!Schema::hasColumn('cms_pages', 'page_type')) {
                $table->string('page_type')->default('default')->nullable();
            }
            if (!Schema::hasColumn('cms_pages', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
            if (!Schema::hasColumn('cms_pages', 'template_name')) {
                $table->string('template_name')->nullable();
            }
        });

        // ── Step 4: Fix template_type enum to include page_builder ───────────────
        DB::statement("ALTER TABLE cms_pages MODIFY template_type ENUM('default','custom','page_builder') DEFAULT 'default'");
    }

    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->text('meta_description')->nullable()->change();
            $table->string('meta_keywords')->nullable()->change();
            $table->string('og_title')->nullable()->change();
            $table->text('og_description')->nullable()->change();
            $table->string('twitter_title')->nullable()->change();
            $table->text('twitter_description')->nullable()->change();
        });
    }
};
