<?php

namespace App\Livewire\Concerns;

use App\Models\User;

trait RedirectsAfterLogin
{
    protected function loginRedirectUrl(User $user): string
    {
        return $user->type === 'backend'
            ? route('admin.dashboard')
            : route('customer.dashboard');
    }
}
