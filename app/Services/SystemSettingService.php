<?php

namespace App\Services;

use Modules\System\Models\SystemSetting;

class SystemSettingService
{
    /**
     * Get a single system setting value.
     */
    public function get(string $key, $default = null)
    {
        return SystemSetting::where('key', $key)->value('value') ?? $default;
    }

    /**
     * Get all system settings as key => value array.
     */
    public function all(): array
    {
        return SystemSetting::pluck('value', 'key')->toArray();
    }
}
