<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cms_banner_items', function (Blueprint $table) {
            $table->id();
            $table->integer('banner_id');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image')->nullable(); // ✅ allow nulls
            $table->string('link')->nullable();
            $table->text('content')->nullable();
            $table->json('buttons')->nullable(); // store multiple buttons as JSON
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(0); // 0 = Inactive, 1 = Active
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_banner_items');
    }
};
