<div class="auth-split">

    {{-- Left panel --}}
    <div class="auth-split__left">
        <a href="{{ route('home') }}" wire:navigate class="auth-brand">
            &#9910; {{ config('app.name', 'LaundryPro') }}
        </a>
        <div class="auth-split__tagline">
            <h2 class="auth-split__heading">One last step.</h2>
            <p class="auth-split__sub">We've sent a verification code to your email. Enter it to complete your sign-in.</p>
        </div>
        <ul class="auth-perks">
            @foreach(['Your account is protected with 2FA', 'Code expires in 10 minutes', 'Didn\'t get it? Request a new one'] as $perk)
            <li class="auth-perk">
                <span class="auth-perk__icon">&#10003;</span>
                {{ $perk }}
            </li>
            @endforeach
        </ul>
    </div>

    {{-- Right panel --}}
    <div class="auth-split__right">
        <div class="auth-form-wrap">

            <div class="auth-form-header" style="text-align:center;">
                <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#0d6efd,#0a2463);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h1 class="auth-form-title">Verify your identity</h1>
                <p class="auth-form-sub">
                    Code sent to <strong>{{ $email ?: 'your email' }}</strong>
                </p>
            </div>

            @if($resent)
                <div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:.875rem;">
                    &#10003; A new code was sent to your email.
                </div>
            @endif

            <form wire:submit="verify" autocomplete="off">
                <div class="auth-field">
                    <label class="auth-label" style="text-align:center;display:block;">Enter 6-digit code</label>
                    <input
                        wire:model="otp"
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="6"
                        autofocus
                        placeholder="000000"
                        class="auth-input @error('otp') is-invalid @enderror"
                        style="text-align:center;font-size:2rem;font-weight:700;letter-spacing:.6rem;padding:14px;"
                    >
                    @error('otp')
                        <div class="auth-error" style="text-align:center;">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-auth-submit" wire:loading.attr="disabled">
                    <span wire:loading wire:target="verify">Verifying…</span>
                    <span wire:loading.remove wire:target="verify">Verify &amp; Sign In</span>
                </button>
            </form>

            <div style="text-align:center;margin-top:20px;font-size:.875rem;color:#6c757d;">
                Didn't receive a code?
                <button type="button" wire:click="resend" wire:loading.attr="disabled"
                    style="background:none;border:none;color:#0d6efd;cursor:pointer;font-size:.875rem;padding:0;text-decoration:underline;">
                    <span wire:loading wire:target="resend">Sending…</span>
                    <span wire:loading.remove wire:target="resend">Resend code</span>
                </button>
            </div>

            <div style="text-align:center;margin-top:12px;">
                <a href="{{ route('customer.login') }}" wire:navigate style="font-size:.875rem;color:#6c757d;text-decoration:none;">
                    &#8592; Back to login
                </a>
            </div>

        </div>
    </div>

</div>
