<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCategory;
use Modules\Inventory\Models\ItemPrice;

class LaundryServiceSeeder extends Seeder
{
    public function run(): void
    {
        Auth::loginUsingId(1);

        // ── Categories ────────────────────────────────────────────
        $washing   = ItemCategory::firstOrCreate(
            ['code' => 'washing'],
            ['name' => ['en' => 'Washing Services', 'ar' => 'خدمات الغسيل']]
        );

        $dryCleaning = ItemCategory::firstOrCreate(
            ['code' => 'dry-cleaning'],
            ['name' => ['en' => 'Dry Cleaning', 'ar' => 'التنظيف الجاف']]
        );

        $specialist = ItemCategory::firstOrCreate(
            ['code' => 'specialist'],
            ['name' => ['en' => 'Specialist Services', 'ar' => 'الخدمات المتخصصة']]
        );

        // ── Services ──────────────────────────────────────────────
        $services = [
            [
                'name'             => ['en' => 'Wash & Fold',          'ar' => 'غسيل وطي'],
                'short_description'=> ['en' => 'Washed, dried, and neatly folded within 24 hours.', 'ar' => ''],
                'description'      => ['en' => 'We wash, dry, and fold your everyday clothes using premium detergents. Returned clean and neatly folded, ready to put away.', 'ar' => ''],
                'category_id'      => $washing->id,
                'turnaround_hours' => 24,
                'unit_label'       => 'per kg',
                'price'            => 3.00,
            ],
            [
                'name'             => ['en' => 'Wash & Iron',          'ar' => 'غسيل وكي'],
                'short_description'=> ['en' => 'Washed, dried, and professionally ironed.', 'ar' => ''],
                'description'      => ['en' => 'All garments washed and returned crisp, wrinkle-free, and ready to wear.', 'ar' => ''],
                'category_id'      => $washing->id,
                'turnaround_hours' => 24,
                'unit_label'       => 'per kg',
                'price'            => 5.00,
            ],
            [
                'name'             => ['en' => 'Express Wash & Fold',  'ar' => 'غسيل سريع وطي'],
                'short_description'=> ['en' => 'Same-day wash and fold — ready in 6 hours.', 'ar' => ''],
                'description'      => ['en' => 'Urgent laundry? Our express service returns your clothes clean and folded the same day.', 'ar' => ''],
                'category_id'      => $washing->id,
                'turnaround_hours' => 6,
                'unit_label'       => 'per kg',
                'price'            => 6.00,
            ],
            [
                'name'             => ['en' => 'Ironing Only',         'ar' => 'كي فقط'],
                'short_description'=> ['en' => 'Professional pressing for already-clean garments.', 'ar' => ''],
                'description'      => ['en' => 'Drop off your clean clothes and we will return them perfectly pressed and wrinkle-free.', 'ar' => ''],
                'category_id'      => $washing->id,
                'turnaround_hours' => 24,
                'unit_label'       => 'per item',
                'price'            => 2.00,
            ],
            [
                'name'             => ['en' => 'Dry Cleaning',         'ar' => 'تنظيف جاف'],
                'short_description'=> ['en' => 'Professional dry cleaning for suits, dresses, and delicate fabrics.', 'ar' => ''],
                'description'      => ['en' => 'Specialist dry cleaning for garments that cannot be washed with water. Stain removal included.', 'ar' => ''],
                'category_id'      => $dryCleaning->id,
                'turnaround_hours' => 48,
                'unit_label'       => 'per item',
                'price'            => 15.00,
            ],
            [
                'name'             => ['en' => 'Dry Clean — Suit',     'ar' => 'تنظيف جاف - بدلة'],
                'short_description'=> ['en' => 'Full suit (jacket + trousers) dry cleaned and pressed.', 'ar' => ''],
                'description'      => ['en' => 'Complete suit dry cleaning, deodorising, and pressing to a sharp finish.', 'ar' => ''],
                'category_id'      => $dryCleaning->id,
                'turnaround_hours' => 48,
                'unit_label'       => 'per suit',
                'price'            => 25.00,
            ],
            [
                'name'             => ['en' => 'Shoe Cleaning',        'ar' => 'تنظيف الأحذية'],
                'short_description'=> ['en' => 'Restore your shoes and sneakers to like-new condition.', 'ar' => ''],
                'description'      => ['en' => 'Deep cleaning, deodorising, and conditioning for all types of shoes and sneakers.', 'ar' => ''],
                'category_id'      => $specialist->id,
                'turnaround_hours' => 48,
                'unit_label'       => 'per pair',
                'price'            => 12.00,
            ],
            [
                'name'             => ['en' => 'Stain Removal',        'ar' => 'إزالة البقع'],
                'short_description'=> ['en' => 'Specialist pre-treatment for tough stains.', 'ar' => ''],
                'description'      => ['en' => 'Our specialists treat stubborn stains using professional-grade products before a full clean.', 'ar' => ''],
                'category_id'      => $specialist->id,
                'turnaround_hours' => 24,
                'unit_label'       => 'per item',
                'price'            => 8.00,
            ],
            [
                'name'             => ['en' => 'Curtain & Linen Cleaning', 'ar' => 'تنظيف الستائر والمفارش'],
                'short_description'=> ['en' => 'Full wash for curtains, bed linen, and large fabric items.', 'ar' => ''],
                'description'      => ['en' => 'We handle bulky items including curtains, duvets, bed sheets, and table linen with care.', 'ar' => ''],
                'category_id'      => $specialist->id,
                'turnaround_hours' => 72,
                'unit_label'       => 'per set',
                'price'            => 25.00,
            ],
        ];

        foreach ($services as $data) {
            $price = $data['price'];
            unset($data['price']);

            $item = Item::firstOrCreate(
                ['name->en' => $data['name']['en'], 'type' => 'service'],
                array_merge($data, [
                    'type'            => 'service',
                    'track_inventory' => false,
                    'status'          => 'active',
                ])
            );

            // Selling price
            ItemPrice::firstOrCreate(
                ['item_id' => $item->id, 'price_type' => 'selling', 'is_default' => true],
                [
                    'price'      => $price,
                    'currency'   => 'USD',
                    'is_default' => true,
                ]
            );
        }
    }
}
