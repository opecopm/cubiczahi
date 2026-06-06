<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('attribute_name_id')->nullable();
            $table->string('name');
            // AttributeType enum: select|color|image|text
            $table->string('type', 20)->default('select');
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_variant_defining')->default(true);
            $table->timestamps();

            $table->foreign('attribute_name_id')->references('id')->on('attribute_names')->onDelete('set null');
            $table->index(['item_id', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_attributes');
    }
};
