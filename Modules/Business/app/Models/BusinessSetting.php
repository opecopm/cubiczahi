<?php

namespace Modules\Business\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    use HasFactory;

    public function getFilterableAttribute($value): array
    {
        return [
            'id' => [
                'operator' => '=',
                'type' => 'text',
                'label' => 'ID',
            ],
            'key' => [
                'operator' => 'like',
                'type' => 'text',
                'label' => 'Key',
            ],
        ];
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['key', 'value'];

    public static function booted()
    {
        static::saved(fn () => cache()->forget('business_settings'));
        static::deleted(fn () => cache()->forget('business_settings'));
    }

    public static function getValue($key)
    {
        return self::where('key', $key)->value('value');
    }
}
