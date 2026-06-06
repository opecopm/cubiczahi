<div class="auth-split">

    {{-- Left panel ─────────────────────────────────────── --}}
    <div class="auth-split__left">
        <a href="{{ lroute('home') }}" wire:navigate class="auth-brand">
            &#10024; {{ config('app.name', 'DetergentShop') }}
        </a>
        <div class="auth-split__tagline">
            <h2 class="auth-split__heading">{{ __('auth.login_heading') }}</h2>
            <p class="auth-split__sub">{{ __('auth.login_sub') }}</p>
        </div>
        <ul class="auth-perks">
            @foreach([__('auth.login_perk_1'), __('auth.login_perk_2'), __('auth.login_perk_3')] as $perk)
            <li class="auth-perk">
                <span class="auth-perk__icon">&#10003;</span>
                {{ $perk }}
            </li>
            @endforeach
        </ul>
        <div class="auth-split__rating">
            <span class="auth-split__stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
            <span>{{ __('auth.login_trusted') }}</span>
        </div>
    </div>

    {{-- Right panel ─────────────────────────────────────── --}}
    <div class="auth-split__right">
        <div class="auth-form-wrap">
            <div class="auth-form-header">
                <h1 class="auth-form-title">{{ __('auth.login_title') }}</h1>
                <p class="auth-form-sub">{{ __('auth.login_subtitle') }}</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-4">{{ session('status') }}</div>
            @endif

            <form wire:submit="login" autocomplete="off">
                <div class="auth-field">
                    <label class="auth-label" for="email">{{ __('auth.email') }}</label>
                    <input wire:model="form.email" id="email" type="email"
                           class="auth-input @error('form.email') is-invalid @enderror"
                           placeholder="your@email.com" required autofocus />
                    @error('form.email')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-field">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="auth-label mb-0" for="password">{{ __('auth.password') }}</label>
                        @if (Route::has('customer.password.request'))
                            <a href="{{ lroute('customer.password.request') }}" wire:navigate class="auth-forgot">
                                {{ __('auth.forgot_password') }}
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
                        {{ __('auth.remember_me') }}
                    </label>
                </div>

                <button type="submit" class="btn-auth-submit">
                    {{ __('auth.sign_in') }}
                </button>
            </form>

            <div class="auth-divider"><span>{{ __('auth.or') }}</span></div>

            <p class="auth-switch">
                {{ __('auth.no_account') }}
                <a href="{{ lroute('customer.register') }}" wire:navigate class="auth-switch__link">
                    {{ __('auth.create_free') }}
                </a>
            </p>
        </div>
    </div>

</div>
