<?php

namespace Modules\Business\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
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
            'name' => [
                'operator' => 'like',
                'type' => 'text',
                'label' => 'Name',
            ],
            'email' => [
                'operator' => 'like',
                'type' => 'text',
                'label' => 'Email',
            ],
        ];
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'email'];
}
