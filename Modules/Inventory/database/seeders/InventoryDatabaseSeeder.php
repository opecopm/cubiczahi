<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Database\Seeders\LaundryServiceSeeder;

class InventoryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            // LaundryServiceSeeder::class,
            LaundryCategoriesAndItemsSeeder::class,
        ]);
    }
}
