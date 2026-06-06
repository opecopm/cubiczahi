<?php

namespace Modules\System\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\System\Models\MenuItem;

class MenuItemFactoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = MenuItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => json_encode(['en' => $this->faker->word]), // Random word for title, encoded as JSON for multilingual support
            'url' => $this->faker->url, // Random URL for the menu item
            'order' => $this->faker->numberBetween(1, 100), // Random order number
            'parent_id' => $this->faker->optional()->randomElement([null, $this->faker->numberBetween(1, 10)]), // Random parent_id or null
            'prefix' => $this->faker->word, // Random prefix
            'icon' => $this->faker->word, // Random word for the icon (or specific icons if needed)
            'created_at' => now(), // Current timestamp
            'updated_at' => now(), // Current timestamp
        ];
    }
}
