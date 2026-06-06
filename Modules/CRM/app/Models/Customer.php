<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Global\Models\Address;
use Modules\Global\Models\GeneralDocument;
use Modules\Global\Models\ReferenceSchema;
use Spatie\Translatable\HasTranslations;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use HasFactory, HasTranslations, Notifiable;

    public $translatable = ['company'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'reference',
        'name',
        'email',
        'phone_code',
        'phone',
        'company',
        'industry',
        'website',
        'crn',
        'trn',
        'customer_group_id',
        'status',
    ];

    const STATUS_SELECT = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            $customer->reference = ReferenceSchema::generate('customer');
        });
    }

    // Assuming 'name' is the searchable column
    public static function getSearchableColumn()
    {
        return 'reference';
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function generalDocuments()
    {
        return $this->morphMany(GeneralDocument::class, 'documentable');
    }

    public function billingAddress()
    {
        return $this->addresses->where('address_type', 'billing_address')->first();
    }

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    public function getFilterableAttribute(): array
    {
        return [
            'status' => [
                'operator' => '=',
                'type' => 'select',
                'options' => self::STATUS_SELECT,
            ],
            'customer_group_id' => [
                'operator' => '=',
                'type' => 'select',
                'options' => CustomerGroup::orderBy('name')->pluck('name', 'id')->toArray(),
            ],
            'created_at' => [
                'operator' => '=',
                'type' => 'date',
            ],
        ];
    }
}
