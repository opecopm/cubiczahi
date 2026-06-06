<?php

namespace Modules\Business\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Business\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::create([
            'name' => 'Saudi Riyal',
            'code' => 'SAR',
            'symbol_left' => 'SR',
            'symbol_right' => null,
            'rate' => 1,
        ]);
    }
}
