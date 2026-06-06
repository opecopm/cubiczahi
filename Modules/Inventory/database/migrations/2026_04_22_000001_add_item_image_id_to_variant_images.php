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
        Schema::table('variant_images', function (Blueprint $table) {
            // Add item_image_id to track which item image is being linked
            $table->unsignedBigInteger('item_image_id')->nullable()->after('variant_id');
            $table->foreign('item_image_id')
                ->references('id')
                ->on('item_images')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variant_images', function (Blueprint $table) {
            $table->dropForeign(['item_image_id']);
            $table->dropColumn('item_image_id');
        });
    }
};
