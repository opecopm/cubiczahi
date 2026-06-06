<?php

namespace Modules\Global\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'name',
        'code',
        'status',
        'is_default',
        'direction',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    const STATUS_SELECT = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    const DIRECTION_SELECT = [
        'ltr' => 'LTR',
        'rtl' => 'RTL',
    ];
}
