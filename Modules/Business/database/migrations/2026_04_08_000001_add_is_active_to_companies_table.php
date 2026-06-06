<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('companies')) {
            return;
        }

        if (Schema::hasColumn('companies', 'is_active')) {
            return;
        }

        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('is_group');
        });

        if (Schema::hasColumn('companies', 'status')) {
            DB::table('companies')->where('status', 'active')->update(['is_active' => 1]);
            DB::table('companies')->where('status', 'inactive')->update(['is_active' => 0]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('companies')) {
            return;
        }

        if (! Schema::hasColumn('companies', 'is_active')) {
            return;
        }

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
