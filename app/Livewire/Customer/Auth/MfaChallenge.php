<?php

namespace App\Livewire\Customer\Auth;

use App\Livewire\Concerns\RedirectsAfterLogin;
use App\Models\User;
use App\Notifications\MfaOtpNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class MfaChallenge extends Component
{
    use RedirectsAfterLogin;
    public string $otp = '';

    public bool $resent = false;

    public function mount(): void
    {
        if (! session()->has('mfa_pending_user_id')) {
            $this->redirect(route('customer.login'), navigate: true);
        }
    }

    public function verify(): void
    {
        $this->validate(['otp' => ['required', 'string', 'size:6']]);

        $user = User::find(session('mfa_pending_user_id'));

        if (
            ! $user ||
            ! $user->otp ||
            ! $user->otp_expires_at ||
            $user->otp_expires_at->isPast() ||
            ! hash_equals($user->otp, $this->otp)
        ) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid or expired code. Please try again.',
            ]);
        }

        $user->update(['otp' => null, 'otp_expires_at' => null]);

        session()->forget('mfa_pending_user_id');

        Auth::login($user);
        Session::regenerate();

        $this->redirect($this->loginRedirectUrl($user), navigate: true);
    }

    public function resend(): void
    {
        $user = User::find(session('mfa_pending_user_id'));

        if (! $user) {
            $this->redirect(route('customer.login'), navigate: true);
            return;
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $user->notify(new MfaOtpNotification($otp));

        $this->otp    = '';
        $this->resent = true;
    }

    public function render()
    {
        $email = User::find(session('mfa_pending_user_id'))?->email ?? '';

        return view(theme_view('livewire.auth.mfa-challenge'), compact('email'))
            ->layout(theme_view('layouts.guest'));
    }
}
