<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('admin.layouts.app')]
class Profile extends Component
{
    // ── Profile fields ─────────────────────────────────────
    public string $first_name   = '';
    public string $last_name    = '';
    public string $email        = '';
    public string $phone_code   = '';
    public string $phone        = '';

    // ── Password fields ────────────────────────────────────
    public string $current_password      = '';
    public string $password              = '';
    public string $password_confirmation = '';

    // ── MFA ────────────────────────────────────────────────
    public bool $mfa_enabled = false;

    // ── UI state ───────────────────────────────────────────
    public string $activeTab = 'profile';

    public function mount(): void
    {
        $user = auth()->user();
        $this->first_name  = $user->first_name  ?? '';
        $this->last_name   = $user->last_name   ?? '';
        $this->email       = $user->email       ?? '';
        $this->phone_code  = $user->phone_code  ?? '';
        $this->phone       = $user->phone       ?? '';
        $this->mfa_enabled = (bool) $user->mfa_enabled;
    }

    public function saveProfile(): void
    {
        $user = auth()->user();

        $this->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['nullable', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone_code' => ['nullable', 'string', 'max:10'],
            'phone'      => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->where('phone_code', $this->phone_code)->ignore($user->id)],
        ]);

        $user->update([
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'phone_code' => $this->phone_code,
            'phone'      => $this->phone,
        ]);

        $this->dispatch('profile-saved');
        session()->flash('profile_success', 'Profile updated successfully.');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->update(['password' => Hash::make($this->password)]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('password_success', 'Password changed successfully.');
    }

    public function updatedMfaEnabled(bool $value): void
    {
        auth()->user()->update(['mfa_enabled' => $value]);
        session()->flash('mfa_success', $value ? '2FA enabled.' : '2FA disabled.');
    }

    public function render()
    {
        return view('admin.livewire.profile');
    }
}
