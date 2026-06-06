<?php

namespace App\Livewire\Customer;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class SecuritySettings extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $mfa_enabled = false;

    public function mount(): void
    {
        $this->mfa_enabled = (bool) auth()->user()->mfa_enabled;
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('password_success', 'Password updated successfully.');
    }

    public function updatedMfaEnabled(bool $enabled): void
    {
        auth()->user()->update([
            'mfa_enabled' => $enabled,
        ]);

        $this->mfa_enabled = $enabled;
        session()->flash('mfa_success', $enabled ? 'Multi-factor authentication is enabled.' : 'Multi-factor authentication is disabled.');
    }

    public function render()
    {
        return view('livewire.customer.security-settings');
    }
}
