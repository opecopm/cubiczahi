<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_id');
            $table->string('value');
            $table->char('hex_code', 7)->nullable();
            $table->string('image_url')->nullable();
            $table->decimal('price_modifier', 10, 2)->default(0);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();

            $table->index(['attribute_id', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_attribute_values');
    }
};
