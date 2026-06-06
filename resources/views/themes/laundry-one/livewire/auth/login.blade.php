<div class="auth-split">

    {{-- Left panel ─────────────────────────────────────── --}}
    <div class="auth-split__left">
        <a href="{{ route('home') }}" wire:navigate class="auth-brand">
            &#9910; {{ config('app.name', 'LaundryPro') }}
        </a>
        <div class="auth-split__tagline">
            <h2 class="auth-split__heading">Clean clothes,<br>zero effort.</h2>
            <p class="auth-split__sub">Sign in and let us handle the laundry while you focus on what matters.</p>
        </div>
        <ul class="auth-perks">
            @foreach(['Free pickup from your door','Ready in 24 hours','100% satisfaction guarantee'] as $perk)
            <li class="auth-perk">
                <span class="auth-perk__icon">&#10003;</span>
                {{ $perk }}
            </li>
            @endforeach
        </ul>
        <div class="auth-split__rating">
            <span class="auth-split__stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
            <span>Trusted by 15,000+ customers</span>
        </div>
    </div>

    {{-- Right panel ─────────────────────────────────────── --}}
    <div class="auth-split__right">
        <div class="auth-form-wrap">
            <div class="auth-form-header">
                <h1 class="auth-form-title">Welcome back</h1>
                <p class="auth-form-sub">Sign in to your account to continue</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-4">{{ session('status') }}</div>
            @endif

            <form wire:submit="login" autocomplete="off">
                <div class="auth-field">
                    <label class="auth-label" for="email">Email address</label>
                    <input wire:model="form.email" id="email" type="email"
                           class="auth-input @error('form.email') is-invalid @enderror"
                           placeholder="your@email.com" required autofocus />
                    @error('form.email')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-field">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="auth-label mb-0" for="password">Password</label>
                        @if (Route::has('customer.password.request'))
                            <a href="{{ route('customer.password.request') }}" wire:navigate class="auth-forgot">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <input wire:model="form.password" id="password" type="password"
                           class="auth-input @error('form.password') is-invalid @enderror"
                           placeholder="••••••••" required />
                    @error('form.password')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-field">
                    <label class="auth-check-label">
                        <input wire:model="form.remember" type="checkbox" class="auth-check" />
                        Keep me signed in
                    </label>
                </div>

                <button type="submit" class="btn-auth-submit">
                    Sign In
                </button>
            </form>

            <div class="auth-divider"><span>or</span></div>

            <p class="auth-switch">
                Don't have an account?
                <a href="{{ route('customer.register') }}" wire:navigate class="auth-switch__link">
                    Create one free
                </a>
            </p>
        </div>
    </div>

</div>
