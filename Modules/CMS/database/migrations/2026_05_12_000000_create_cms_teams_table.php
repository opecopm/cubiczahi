<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_teams', function (Blueprint $table) {
            $table->increments('id');

            // Translatable fields (stored as JSON via Spatie Translatable)
            $table->json('name');
            $table->json('designation')->nullable();
            $table->json('phone')->nullable();
            $table->json('bio')->nullable();
            $table->json('message')->nullable();
            $table->json('facebook')->nullable();
            $table->json('twitter')->nullable();
            $table->json('linkedin')->nullable();
            $table->json('instagram')->nullable();

            // Non-translatable fields
            $table->string('email')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('status')->default(1);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_teams');
    }
};
