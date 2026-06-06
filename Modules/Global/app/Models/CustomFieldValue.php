<?php

namespace Modules\Global\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldValue extends Model
{
    protected $fillable = [
        'custom_field_id',
        'customizable_id', // Corrected to match the schema
        'customizable_type', // Corrected to match the schema
        'value',
    ];

    /*sample data
    [
        '1', //custom_field_id
        '2', //asset_id
        'assets', //type name,
        '123456'
    ]*/

    public function customizable()
    {
        return $this->morphTo();
    }

    public function customField()
    {
        return $this->belongsTo(CustomField::class);
    }
}
