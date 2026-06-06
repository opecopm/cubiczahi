<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class LocationScope implements Scope
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

        $locationIds = $user->locations()->pluck('location_id');

        if ($user->location_id) {
            $locationIds = $locationIds->push((int) $user->location_id)->unique();
        }

        $builder->whereIn('location_id', $locationIds);
    }
}
