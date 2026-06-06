<?php

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['key', 'value'];

    /* get value */

    public static function booted()
    {
        static::saved(fn () => cache()->forget('system_settings'));
        static::deleted(fn () => cache()->forget('system_settings'));
    }

    /* get value */
    public static function getValue($key)
    {
        return self::where('key', $key)->value('value');
    }
}
