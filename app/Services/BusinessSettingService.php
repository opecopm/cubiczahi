<?php

namespace App\Services;

use Modules\Business\Models\BusinessSetting;

class BusinessSettingService
{
    /**
     * Get a single business setting value.
     */
    public function get(string $key, $default = null)
    {
        return BusinessSetting::where('key', $key)->value('value') ?? $default;
    }

    /**
     * Get all business settings as key => value array.
     */
    public function all(): array
    {
        return BusinessSetting::pluck('value', 'key')->toArray();
    }
}
