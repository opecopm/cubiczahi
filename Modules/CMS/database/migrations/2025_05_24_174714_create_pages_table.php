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
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->string('slug')->unique();
            $table->json('content')->nullable();
            $table->string('status')->default('draft'); // draft, published
            $table->enum('template_type', ['default', 'custom'])->default('default');
            $table->string('template_name')->nullable();
            $table->integer('parent_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_url')->nullable();
            $table->string('og_type')->default('article')->nullable();
            $table->string('og_site_name')->nullable();
            $table->string('og_locale')->default('en_US')->nullable();
            $table->timestamp('published_time')->nullable();
            $table->timestamp('modified_time')->nullable();
            $table->string('twitter_card')->default('summary')->nullable();
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->json('breadcrumb_title')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_pages');
    }
};
