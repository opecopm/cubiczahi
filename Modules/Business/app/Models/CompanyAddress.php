<?php

namespace Modules\Business\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CompanyAddress extends Model
{
    use HasFactory, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    public $translatable = [
        'label',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
    ];

    protected $fillable = [
        'company_id',
        'type',
        'label',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'postal_code',
        'is_primary',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
