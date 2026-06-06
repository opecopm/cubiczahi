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
        Schema::create('cms_blogs', function (Blueprint $table) {
            $table->id();

            // 🔹 Translatable JSON fields (Spatie Translatable)
            $table->json('title');
            $table->json('content');
            $table->json('excerpt')->nullable();

            // 🔹 Slug (choose global OR translatable)
            $table->json('slug');
            // $table->string('slug')->unique(); // if global slug instead

            // 🔹 Other blog fields
            $table->string('featured_image')->nullable();
            $table->boolean('allow_comments')->default(true);
            $table->boolean('allow_pings')->default(true);
            $table->json('tags')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_blogs');
    }
};
