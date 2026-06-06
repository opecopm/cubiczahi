<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone_code')) {
                $table->string('phone_code', 10)->nullable()->after('type');
            }
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('phone_code');
            }
        });

        // Unique composite index: same phone_code+phone cannot belong to two users
        Schema::table('users', function (Blueprint $table) {
            $table->unique(['phone_code', 'phone'], 'users_phone_code_phone_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_phone_code_phone_unique');
            $table->dropColumn(['phone_code', 'phone']);
        });
    }
};
