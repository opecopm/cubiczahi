<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToDefaultCompany
{
    protected static function bootBelongsToDefaultCompany()
    {
        static::addGlobalScope('default_company', function (Builder $builder) {
            if (auth()->check() && auth()->user()->defaultCompany()) {
                $builder->where('company_id', auth()->user()->defaultCompany()->id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->defaultCompany()) {
                $model->company_id = auth()->user()->defaultCompany()->id;
            }
        });
    }
}
