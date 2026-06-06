<?php

namespace Modules\Global\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['address_type', 'country', 'state', 'city', 'line1', 'line2', 'postal_code', 'addressable_id', 'addressable_type'];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
