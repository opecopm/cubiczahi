<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Add temporary JSON column and migrate existing string names into translations
        Schema::table('cms_blog_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('cms_blog_categories', 'name_tmp')) {
                $table->json('name_tmp')->nullable();
            }
        });

        $default = config('app.locale', 'en');
        $rows = DB::table('cms_blog_categories')->select('id', 'name')->get();
        foreach ($rows as $row) {
            $json = json_encode([$default => $row->name]);
            DB::table('cms_blog_categories')->where('id', $row->id)->update(['name_tmp' => $json]);
        }

        // Drop old name column and rename tmp to name
        DB::statement('ALTER TABLE cms_blog_categories DROP COLUMN name');
        DB::statement('ALTER TABLE cms_blog_categories CHANGE COLUMN name_tmp name JSON');
    }

    public function down(): void
    {
        // Revert JSON name back to string (default locale)
        Schema::table('cms_blog_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('cms_blog_categories', 'name_str')) {
                $table->string('name_str')->nullable();
            }
        });

        $default = config('app.locale', 'en');
        $rows = DB::table('cms_blog_categories')->select('id', 'name')->get();
        foreach ($rows as $row) {
            try {
                $data = json_decode($row->name, true);
                $val = is_array($data) ? ($data[$default] ?? reset($data) ?? null) : null;
            } catch (\Throwable $e) {
                $val = null;
            }
            DB::table('cms_blog_categories')->where('id', $row->id)->update(['name_str' => $val]);
        }

        DB::statement('ALTER TABLE cms_blog_categories DROP COLUMN name');
        DB::statement('ALTER TABLE cms_blog_categories CHANGE COLUMN name_str name VARCHAR(255)');
    }
};