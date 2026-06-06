<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_testimonials', function (Blueprint $table) {
            $table->increments('id');

            // Translatable fields (stored as JSON via Spatie Translatable)
            $table->json('name');
            $table->json('designation');
            $table->json('company')->nullable();
            $table->json('website')->nullable();
            $table->json('location')->nullable();
            $table->json('phone')->nullable();
            $table->json('message');
            $table->json('about')->nullable();

            // Non-translatable fields
            $table->string('email')->nullable();
            $table->string('image')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_path')->nullable();
            $table->unsignedInteger('rating')->nullable();
            $table->boolean('featured')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_testimonials');
    }
};
