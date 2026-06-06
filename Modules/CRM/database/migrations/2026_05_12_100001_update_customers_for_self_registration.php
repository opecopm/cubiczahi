<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // email and phone are NOT unique for customers
            $table->dropUnique(['email']);

            if (! Schema::hasColumn('customers', 'phone_code')) {
                $table->string('phone_code', 10)->nullable()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('phone_code');
            $table->string('email')->unique()->change();
        });
    }
};
