<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'short_description')) {
                $table->text('short_description')->nullable()->after('description');
            }
            $table->unsignedSmallInteger('turnaround_hours')->nullable()->after('track_inventory');
            $table->string('unit_label', 50)->nullable()->after('turnaround_hours');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['turnaround_hours', 'unit_label']);
        });
    }
};
