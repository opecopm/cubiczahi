<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'userable_type')) {
                $table->string('userable_type')->nullable()->after('type');
            }
            if (! Schema::hasColumn('users', 'userable_id')) {
                $table->unsignedBigInteger('userable_id')->nullable()->after('userable_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['userable_type', 'userable_id']);
        });
    }
};
