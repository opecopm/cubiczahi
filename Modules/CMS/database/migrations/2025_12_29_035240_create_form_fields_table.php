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
        Schema::create('cms_form_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id')->index();
            $table->string('type'); // text, email, password, number, textarea, select, checkbox, radio, file, date, etc.
            $table->json('label'); // Translatable
            $table->string('name'); // Slug for input name
            $table->json('placeholder')->nullable(); // Translatable
            $table->json('help_text')->nullable(); // Translatable
            $table->json('options')->nullable(); // For select, radio, checkbox: [{"label":"Option 1","value":"1"}]
            $table->json('validation_rules')->nullable(); // required, min, max, mimes, etc.
            $table->integer('order')->default(0);
            $table->string('width')->default('12'); // Grid column width (1-12)
            $table->boolean('is_required')->default(false);
            $table->json('conditional_logic')->nullable(); // Show/Hide rules
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_form_fields');
    }
};
