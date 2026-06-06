<?php

namespace Modules\Business\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
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
            'status' => [
                'operator' => '=',
                'type' => 'select',
                'label' => 'Status',
                'options' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ],
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
        ];
    }

    protected $fillable = [
        'name',
        'rate',
        'status',
        'is_default',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
