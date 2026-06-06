<?php

namespace Modules\Global\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Global\Models\Language;

class GlobalDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::firstOrCreate(['code' => 'en'], [
            'name'       => 'English',
            'status'     => 'active',
            'is_default' => true,
            'direction'  => 'ltr',
        ]);

        Language::firstOrCreate(['code' => 'ar'], [
            'name'       => 'العربية',
            'status'     => 'active',
            'is_default' => false,
            'direction'  => 'rtl',
        ]);
    }
}
