<?php

namespace App\Livewire\Admin\Auth;

use App\Livewire\Concerns\RedirectsAfterLogin;
use App\Models\User;
use App\Notifications\MfaOtpNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('admin.layouts.guest')]
class MfaChallenge extends Component
{
    use RedirectsAfterLogin;
    public string $otp = '';

    public bool $resent = false;

    public function mount(): void
    {
        if (! session()->has('mfa_pending_user_id')) {
            $this->redirect(route('admin.login'), navigate: true);
        }
    }

    public function verify(): void
    {
        $this->validate(['otp' => ['required', 'string', 'size:6']]);

        $userId = session('mfa_pending_user_id');
        $user   = User::find($userId);

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

        // Clear OTP and complete login
        $user->update(['otp' => null, 'otp_expires_at' => null]);

        session()->forget('mfa_pending_user_id');

        Auth::login($user);
        Session::regenerate();

        $this->redirect($this->loginRedirectUrl($user), navigate: true);
    }

    public function resend(): void
    {
        $userId = session('mfa_pending_user_id');
        $user   = User::find($userId);

        if (! $user) {
            $this->redirect(route('admin.login'), navigate: true);
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
        $email = '';
        if ($userId = session('mfa_pending_user_id')) {
            $user  = User::find($userId);
            $email = $user ? $user->email : '';
        }

        return view('admin.livewire.auth.mfa-challenge', compact('email'));
    }
}
