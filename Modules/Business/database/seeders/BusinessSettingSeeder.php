<?php

namespace Modules\Business\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Business\Models\BusinessSetting;

class BusinessSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BusinessSetting::create([
            'key' => 'default_currency',
            'value' => 'SAR', // Assuming 'USD' as the default currency code
        ]);
    }
}
