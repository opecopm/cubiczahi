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
        Schema::table('cms_forms', function (Blueprint $table) {
            $table->boolean('use_honeypot')->default(false)->after('use_captcha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_forms', function (Blueprint $table) {
            $table->dropColumn('use_honeypot');
        });
    }
};
