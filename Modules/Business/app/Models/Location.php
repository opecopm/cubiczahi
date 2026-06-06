<?php

namespace Modules\Business\Models;

use App\Models\User;
use App\Traits\BelongsToDefaultCompany;
use App\Traits\TrackUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use BelongsToDefaultCompany, HasFactory, TrackUser;

    public function getFilterableAttribute($value): array
    {
        return [
            'id' => [
                'operator' => '=',
                'type' => 'text',
                'label' => 'ID',
            ],
            'company_id' => [
                'operator' => '=',
                'type' => 'select',
                'label' => 'Company',
                'options' => Company::query()
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->mapWithKeys(function ($company) {
                        return [$company->id => $company->getTranslation('name', 'en')];
                    })
                    ->toArray(),
            ],
            'parent_id' => [
                'operator' => '=',
                'type' => 'select',
                'label' => 'Parent',
                'options' => self::query()
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray(),
            ],
            'code' => [
                'operator' => 'like',
                'type' => 'text',
                'label' => 'Code',
            ],
            'name' => [
                'operator' => 'like',
                'type' => 'text',
                'label' => 'Name',
            ],
            'type' => [
                'operator' => '=',
                'type' => 'select',
                'label' => 'Type',
                'options' => self::TYPE_SELECT,
            ],
            'status' => [
                'operator' => '=',
                'type' => 'select',
                'label' => 'Status',
                'options' => self::STATUS_SELECT,
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
        ];
    }

    protected $fillable = [
        'company_id',
        'parent_id',
        'code',
        'name',
        'type',
        'description',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const TYPE_SELECT = [
        'warehouse' => 'Warehouse',
        'technician' => 'Technician',
        'van' => 'Van',
        'department' => 'Department',
        'service_center' => 'Service Center',
        'asd' => 'ASD',
        'branch' => 'Branch',
        'building' => 'Building',
        'floor' => 'Floor',
        'room' => 'Room',
        'rack' => 'Rack',
        'yard' => 'Yard',
        'virtual' => 'Virtual',
        'other' => 'Other',
    ];

    public const STATUS_SELECT = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_locations');
    }
}
