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
        Schema::table('cms_page_sections', function (Blueprint $table) {
            $table->string('column_width', 10)->default('12')->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_page_sections', function (Blueprint $table) {
            $table->dropColumn('column_width');
        });
    }
};
