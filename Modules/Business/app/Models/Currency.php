<?php

namespace Modules\Business\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
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
            'status' => [
                'operator' => '=',
                'type' => 'select',
                'label' => 'Status',
                'options' => self::STATUS_SELECT,
            ],
            'is_default' => [
                'operator' => '=',
                'type' => 'select',
                'label' => 'Default',
                'options' => [
                    1 => 'Yes',
                    0 => 'No',
                ],
            ],
            'code' => [
                'operator' => 'like',
                'type' => 'text',
                'label' => 'Code',
            ],
        ];
    }

    /* status */

    const STATUS_SELECT = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'code', 'symbol_left', 'symbol_right', 'rate', 'status', 'is_default'];
}
