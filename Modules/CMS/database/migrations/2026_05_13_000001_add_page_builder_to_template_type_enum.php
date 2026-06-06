<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE cms_pages MODIFY COLUMN template_type ENUM('default', 'custom', 'page_builder') DEFAULT 'default'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE cms_pages MODIFY COLUMN template_type ENUM('default', 'custom') DEFAULT 'default'");
    }
};
