<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCategory;
use Modules\Inventory\Models\ItemAttributeName;
use Modules\Inventory\Models\ItemVariant;

class LaundryCategoriesAndItemsSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have a user context for tracking
        if (!Auth::check()) {
            Auth::loginUsingId(1);
        }

        // ── Item Categories ────────────────────────────────────────────
        $categories = [
            ['code' => 'clothes', 'name' => ['en' => 'Clothes']],
            ['code' => 'blankets_bedding', 'name' => ['en' => 'Blankets & Bedding']],
            ['code' => 'bags', 'name' => ['en' => 'Bags']],
            ['code' => 'shoes', 'name' => ['en' => 'Shoes']],
            ['code' => 'household', 'name' => ['en' => 'Household']],
            ['code' => 'curtains', 'name' => ['en' => 'Curtains']],
            ['code' => 'carpets', 'name' => ['en' => 'Carpets']],
        ];

        $createdCategories = [];
        foreach ($categories as $categoryData) {
            $category = ItemCategory::firstOrCreate(
                ['code' => $categoryData['code']],
                ['name' => $categoryData['name']]
            );
            $createdCategories[$categoryData['code']] = $category;
        }

        // ── Service Items ──────────────────────────────────────────────
        $serviceItems = [
            // Clothes
            ['name' => ['en' => 'Shirt'], 'category_code' => 'clothes'],
            ['name' => ['en' => 'T-Shirt'], 'category_code' => 'clothes'],
            ['name' => ['en' => 'Full Suit'], 'category_code' => 'clothes'],
            ['name' => ['en' => 'Coat / Blazer'], 'category_code' => 'clothes'],
            ['name' => ['en' => 'Pant'], 'category_code' => 'clothes'],
            ['name' => ['en' => 'Jeans'], 'category_code' => 'clothes'],
            ['name' => ['en' => 'Jacket'], 'category_code' => 'clothes'],
            ['name' => ['en' => 'Sweater'], 'category_code' => 'clothes'],
            ['name' => ['en' => 'Abaya'], 'category_code' => 'clothes'],
            ['name' => ['en' => 'Thobe'], 'category_code' => 'clothes'],
            ['name' => ['en' => 'Dress'], 'category_code' => 'clothes'],

            // Blankets & Bedding
            ['name' => ['en' => 'Single Blanket'], 'category_code' => 'blankets_bedding'],
            ['name' => ['en' => 'Double Blanket'], 'category_code' => 'blankets_bedding'],
            ['name' => ['en' => 'Comforter'], 'category_code' => 'blankets_bedding'],
            ['name' => ['en' => 'Bedsheet'], 'category_code' => 'blankets_bedding'],
            ['name' => ['en' => 'Pillow Cover'], 'category_code' => 'blankets_bedding'],

            // Bags
            ['name' => ['en' => 'Shoulder Bag'], 'category_code' => 'bags'],
            ['name' => ['en' => 'Hand Carry'], 'category_code' => 'bags'],
            ['name' => ['en' => 'Medium Bag'], 'category_code' => 'bags'],
            ['name' => ['en' => 'Large Bag'], 'category_code' => 'bags'],
            ['name' => ['en' => 'Leather Bag'], 'category_code' => 'bags'],
            ['name' => ['en' => 'Backpack'], 'category_code' => 'bags'],

            // Shoes
            ['name' => ['en' => 'Leather Shoes'], 'category_code' => 'shoes'],
            ['name' => ['en' => 'Sneaker Shoes'], 'category_code' => 'shoes'],
            ['name' => ['en' => 'Sports Shoes'], 'category_code' => 'shoes'],
            ['name' => ['en' => 'Sandals'], 'category_code' => 'shoes'],
            ['name' => ['en' => 'Boots'], 'category_code' => 'shoes'],
        ];

        $createdItems = [];
        foreach ($serviceItems as $itemData) {
            $category = $createdCategories[$itemData['category_code']] ?? null;

            if ($category) {
                $item = Item::firstOrCreate(
                    ['name->en' => $itemData['name']['en'], 'type' => 'service'],
                    [
                        'type' => 'service',
                        'name' => $itemData['name'],
                        'category_id' => $category->id,
                        'status' => 'active',
                        'track_inventory' => false,
                        'is_serialized' => false,
                        'has_variants' => false,
                    ]
                );
                $createdItems[$itemData['name']['en']] = $item;
            }
        }

        // ── Attribute Names (Laundry Services) ──────────────────────────
        $attributeNames = [
            [
                'slug' => 'service-type',
                'name' => ['en' => 'Service Type'],
                'description' => ['en' => 'Choose your laundry service'],
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'turnaround-time',
                'name' => ['en' => 'Turnaround Time'],
                'description' => ['en' => 'How quickly do you need it?'],
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'special-care',
                'name' => ['en' => 'Special Care'],
                'description' => ['en' => 'Optional: Add special treatment'],
                'is_required' => false,
                'sort_order' => 3,
            ],
        ];

        $createdAttributes = [];
        foreach ($attributeNames as $attrData) {
            $attr = ItemAttributeName::firstOrCreate(
                ['slug' => $attrData['slug']],
                $attrData
            );
            $createdAttributes[$attrData['slug']] = $attr;
        }

        // ── Item Variants (Options for each service) ───────────────────
        // Example: Shirt with Service Type, Turnaround Time, and Special Care
        $shirt = $createdItems['Shirt'] ?? null;
        if ($shirt) {
            $serviceTypeAttr = $createdAttributes['service-type'];
            $turnaroundAttr = $createdAttributes['turnaround-time'];
            $specialCareAttr = $createdAttributes['special-care'];

            // Service Type variants
            ItemVariant::firstOrCreate(
                ['item_id' => $shirt->id, 'attribute_id' => $serviceTypeAttr->id, 'name->en' => 'Wash Only'],
                [
                    'item_id' => $shirt->id,
                    'attribute_id' => $serviceTypeAttr->id,
                    'name' => ['en' => 'Wash Only'],
                    'note' => ['en' => 'Basic washing'],
                    'price_difference' => 0.00,
                    'is_default' => true,
                    'sort_order' => 1,
                    'status' => 'active',
                ]
            );

            ItemVariant::firstOrCreate(
                ['item_id' => $shirt->id, 'attribute_id' => $serviceTypeAttr->id, 'name->en' => 'Wash + Iron'],
                [
                    'item_id' => $shirt->id,
                    'attribute_id' => $serviceTypeAttr->id,
                    'name' => ['en' => 'Wash + Iron'],
                    'note' => ['en' => 'Wash and iron included'],
                    'price_difference' => 5.00,
                    'is_default' => false,
                    'sort_order' => 2,
                    'status' => 'active',
                ]
            );

            ItemVariant::firstOrCreate(
                ['item_id' => $shirt->id, 'attribute_id' => $serviceTypeAttr->id, 'name->en' => 'Dry Clean'],
                [
                    'item_id' => $shirt->id,
                    'attribute_id' => $serviceTypeAttr->id,
                    'name' => ['en' => 'Dry Clean'],
                    'note' => ['en' => 'Professional dry cleaning'],
                    'price_difference' => 10.00,
                    'is_default' => false,
                    'sort_order' => 3,
                    'status' => 'active',
                ]
            );

            // Turnaround Time variants
            ItemVariant::firstOrCreate(
                ['item_id' => $shirt->id, 'attribute_id' => $turnaroundAttr->id, 'name->en' => 'Standard (48h)'],
                [
                    'item_id' => $shirt->id,
                    'attribute_id' => $turnaroundAttr->id,
                    'name' => ['en' => 'Standard (48h)'],
                    'note' => ['en' => 'Ready in 2 days'],
                    'price_difference' => 0.00,
                    'is_default' => true,
                    'sort_order' => 1,
                    'status' => 'active',
                ]
            );

            ItemVariant::firstOrCreate(
                ['item_id' => $shirt->id, 'attribute_id' => $turnaroundAttr->id, 'name->en' => 'Express (24h)'],
                [
                    'item_id' => $shirt->id,
                    'attribute_id' => $turnaroundAttr->id,
                    'name' => ['en' => 'Express (24h)'],
                    'note' => ['en' => 'Ready in 1 day'],
                    'price_difference' => 10.00,
                    'is_default' => false,
                    'sort_order' => 2,
                    'status' => 'active',
                ]
            );

            ItemVariant::firstOrCreate(
                ['item_id' => $shirt->id, 'attribute_id' => $turnaroundAttr->id, 'name->en' => 'Rush (Same day)'],
                [
                    'item_id' => $shirt->id,
                    'attribute_id' => $turnaroundAttr->id,
                    'name' => ['en' => 'Rush (Same day)'],
                    'note' => ['en' => 'Ready the same day'],
                    'price_difference' => 20.00,
                    'is_default' => false,
                    'sort_order' => 3,
                    'status' => 'active',
                ]
            );

            // Special Care variants (optional)
            ItemVariant::firstOrCreate(
                ['item_id' => $shirt->id, 'attribute_id' => $specialCareAttr->id, 'name->en' => 'Delicate Fabric'],
                [
                    'item_id' => $shirt->id,
                    'attribute_id' => $specialCareAttr->id,
                    'name' => ['en' => 'Delicate Fabric'],
                    'note' => ['en' => 'For silk, wool, etc.'],
                    'price_difference' => 5.00,
                    'is_default' => false,
                    'sort_order' => 1,
                    'status' => 'active',
                ]
            );

            ItemVariant::firstOrCreate(
                ['item_id' => $shirt->id, 'attribute_id' => $specialCareAttr->id, 'name->en' => 'Stain Removal'],
                [
                    'item_id' => $shirt->id,
                    'attribute_id' => $specialCareAttr->id,
                    'name' => ['en' => 'Stain Removal'],
                    'note' => ['en' => 'Professional stain treatment'],
                    'price_difference' => 3.00,
                    'is_default' => false,
                    'sort_order' => 2,
                    'status' => 'active',
                ]
            );
        }
    }
}
