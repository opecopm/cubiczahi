<?php

namespace Modules\Business\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Company extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    public $translatable = ['name'];

    protected $appends = [
        'logo_url',
        'header_url',
        'footer_url',
        'stamp_url',
    ];

    public function getFilterableAttribute($value): array
    {
        return [
            'id' => [
                'operator' => '=',
                'type' => 'text',
                'label' => 'ID',
            ],
            'code' => [
                'operator' => 'like',
                'type' => 'text',
                'label' => 'Code',
            ],
            'crn' => [
                'operator' => 'like',
                'type' => 'text',
                'label' => 'CRN',
            ],
            'trn' => [
                'operator' => 'like',
                'type' => 'text',
                'label' => 'TRN',
            ],
            'currency' => [
                'operator' => '=',
                'type' => 'select',
                'label' => 'Currency',
                'options' => Currency::query()
                    ->orderBy('code')
                    ->pluck('code', 'code')
                    ->toArray(),
            ],
            'is_group' => [
                'operator' => '=',
                'type' => 'select',
                'label' => 'Is Group',
                'options' => [
                    1 => 'Yes',
                    0 => 'No',
                ],
            ],
            'is_active' => [
                'operator' => '=',
                'type' => 'select',
                'label' => 'Active',
                'options' => [
                    1 => 'Yes',
                    0 => 'No',
                ],
            ],
            'parent_id' => [
                'operator' => '=',
                'type' => 'select',
                'label' => 'Parent',
                'options' => self::query()
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->mapWithKeys(function ($company) {
                        return [$company->id => $company->getTranslation('name', 'en')];
                    })
                    ->toArray(),
            ],
        ];
    }

    protected $fillable = [
        'name',
        'code',
        'parent_id',
        'crn',
        'trn',
        'email',
        'phone',
        'website',
        'invoice_code',
        'currency',
        'is_group',
        'is_active',
        'status',
        // 'hr_id',
        // 'vp_id',
        'created_by',
        'updated_by',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_companies')
            ->withPivot('is_default')
            ->withTimestamps();
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('company_logo') ?: asset('assets/img/logo.png');
    }

    public function getHeaderUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('company_header') ?: asset('assets/img/header.png');
    }

    public function getFooterUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('company_footer') ?: asset('assets/img/footer.png');
    }

    public function getStampUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('company_stamp') ?: asset('assets/img/stamp.png');
    }

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Company::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Company::class, 'parent_id');
    }

    public function addresses()
    {
        return $this->morphMany(\Modules\Global\Models\Address::class, 'addressable');
    }

    public function primaryAddress()
    {
        return $this->morphOne(\Modules\Global\Models\Address::class, 'addressable');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function settings()
    {
        return $this->hasOne(BusinessSetting::class);
    }
}
