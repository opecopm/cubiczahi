<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('media_links')) {
            return;
        }

        Schema::create('media_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_asset_id')
                ->constrained('media_assets')
                ->cascadeOnDelete();
            $table->string('linkable_type');
            $table->string('linkable_id');
            $table->string('usage')->nullable()->index();
            $table->string('collection_name')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false)->index();
            $table->json('context')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->index(['linkable_type', 'linkable_id'], 'media_links_linkable_index');
            $table->index(['media_asset_id', 'usage'], 'media_links_asset_usage_idx');
            $table->index(['linkable_type', 'linkable_id', 'usage'], 'media_links_linkable_usage_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_links');
    }
};
