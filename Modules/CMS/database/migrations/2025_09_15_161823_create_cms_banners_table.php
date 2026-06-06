<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cms_banners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('status')->default(0); // 0 = Inactive, 1 = Active
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_banners');
    }
};
