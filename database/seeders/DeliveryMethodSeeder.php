<?php

namespace Database\Seeders;

use App\Models\DeliveryMethod;
use Illuminate\Database\Seeder;

class DeliveryMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name'            => 'Self Drop-off & Pickup',
                'slug'            => 'dropoff',
                'description'     => 'Drop off at our store and collect when ready',
                'icon'            => '🏪',
                'estimated_label' => 'Ready in 2–3 days',
                'price'           => 0,
                'sort_order'      => 1,
            ],
            [
                'name'            => 'Home Pickup & Delivery',
                'slug'            => 'home_delivery',
                'description'     => 'We pick up from your door and deliver back',
                'icon'            => '🚗',
                'estimated_label' => '2–3 days',
                'price'           => 15,
                'sort_order'      => 2,
            ],
            [
                'name'            => 'Express Home Service',
                'slug'            => 'express',
                'description'     => 'Priority pickup and delivery',
                'icon'            => '⚡',
                'estimated_label' => 'Next day',
                'price'           => 30,
                'sort_order'      => 3,
            ],
            [
                'name'            => 'Same-Day Rush',
                'slug'            => 'same_day',
                'description'     => 'Urgent same-day pickup and return',
                'icon'            => '🔥',
                'estimated_label' => 'Same day',
                'price'           => 50,
                'sort_order'      => 4,
            ],
        ];

        foreach ($methods as $method) {
            DeliveryMethod::updateOrCreate(['slug' => $method['slug']], $method);
        }
    }
}
