<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        if ($user->hasRole('Admin') || $user->hasRole('Manager')) {
            return;
        }

        $companyIds = $user->companies()->pluck('companies.id');

        $builder->whereIn('company_id', $companyIds);
    }
}
