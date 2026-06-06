<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Models\ItemCategory;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remove all existing categories
        ItemCategory::query()->forceDelete();

        $categories = [
            'Electronics & Mobiles' => [
                'Smartphones',
                'Laptops & Computers',
                'Accessories',
                'Gaming',
            ],
            'Fashion' => [
                'Men\'s Clothing',
                'Women\'s Clothing',
                'Children\'s Clothing',
                'Shoes',
                'Bags & Accessories',
            ],
            'Beauty & Personal Care' => [
                'Cosmetics',
                'Skincare',
                'Haircare',
                'Fragrances',
            ],
            'Home & Kitchen' => [
                'Furniture',
                'Home Decor',
                'Kitchen Appliances',
                'Bedding & Bath',
            ],
            'Supermarket & Grocery' => [
                'Food & Beverages',
                'Snacks',
                'Organic Products',
                'Household Supplies',
                'Detergents & Cleaners',
            ],
            'Baby & Kids' => [
                'Baby Care',
                'Toys & Games',
                'Strollers',
                'School Supplies',
            ],
            'Sports & Outdoors' => [
                'Exercise Equipment',
                'Camping Gear',
                'Sports Apparel',
                'Cycling',
            ],
            'Books, Stationery & Office Supplies' => [
                'Books',
                'Educational Materials',
                'Office Equipment',
            ],
            'Jewelry & Watches' => [
                'Fashion Jewelry',
                'Watches',
                'Luxury Accessories',
            ],
            'Islamic & Cultural Products' => [
                'Prayer Mats',
                'Qurans & Islamic Books',
                'Modest Fashion (Abayas, Thobes)',
                'Hajj & Umrah Accessories',
            ],
        ];

        foreach ($categories as $parentName => $children) {
            $parent = ItemCategory::create([
                'name' => ['en' => $parentName],
                'code' => Str::slug($parentName),
                'parent_id' => null,
            ]);

            foreach ($children as $childName) {
                ItemCategory::create([
                    'name' => ['en' => $childName],
                    'code' => Str::slug($childName),
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }
}
