<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old e-commerce variant system
        Schema::dropIfExists('variant_attribute_values');
        Schema::dropIfExists('item_attribute_values');
        Schema::dropIfExists('item_attributes');
        Schema::dropIfExists('item_variants');

        // Extend attribute_names with laundry-useful columns
        Schema::table('attribute_names', function (Blueprint $table) {
            // e.g. "Choose your load size" shown above the options
            $table->json('description')->nullable()->after('name');
            // force customer to pick one option before ordering
            $table->boolean('is_required')->default(true)->after('slug');
            // display order of attribute groups on the item page
            $table->unsignedInteger('sort_order')->default(0)->after('is_required');
            $table->string('status')->default('active')->after('sort_order');
        });

        // Simple flat item_variants for laundry
        Schema::create('item_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('attribute_id'); // attribute_names.id

            // The selectable option label shown to the customer
            $table->json('name');                       // translatable: "Small" / "صغير"

            // Optional customer-facing hint shown under the option
            // e.g. "Up to 5 kg", "Ready within 3 hours", "For silk & wool"
            $table->json('note')->nullable();

            // Signed amount added to / subtracted from the item base price
            $table->decimal('price_difference', 10, 2)->default(0);

            // Which option is pre-selected when the item page loads
            $table->boolean('is_default')->default(false);

            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['item_id', 'attribute_id']);
            $table->index(['item_id', 'attribute_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_variants');

        Schema::table('attribute_names', function (Blueprint $table) {
            $table->dropColumn(['description', 'is_required', 'sort_order', 'status']);
        });

        // Recreate old tables (structure only)
        Schema::create('item_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('attribute_name_id')->nullable();
            $table->string('name');
            $table->string('type', 20)->default('select');
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_variant_defining')->default(true);
            $table->timestamps();
        });

        Schema::create('item_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_id');
            $table->string('value');
            $table->decimal('price_modifier', 10, 2)->default(0);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('item_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('sku', 100)->unique();
            $table->decimal('price', 12, 2);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('variant_attribute_values', function (Blueprint $table) {
            $table->unsignedBigInteger('variant_id');
            $table->unsignedBigInteger('attribute_value_id');
            $table->primary(['variant_id', 'attribute_value_id']);
        });
    }
};
