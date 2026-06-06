<?php

namespace Modules\System\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\System\Models\Menu;

class MenuFactoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Menu::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word, // Random word as menu name
            'url' => $this->faker->url, // Random URL
            'order' => $this->faker->numberBetween(1, 100), // Random order number
            'parent_id' => null, // Change this if you want to set a parent for nested menus
            'prefix' => null, // You can set this as needed
            'icon' => $this->faker->word, // Random word as icon (or use specific icons)
            'created_at' => now(), // Current timestamp
            'updated_at' => now(), // Current timestamp
        ];
    }
}
