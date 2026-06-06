<div class="auth-split">

    {{-- Left panel ─────────────────────────────────────── --}}
    <div class="auth-split__left">
        <a href="{{ lroute('home') }}" wire:navigate class="auth-brand">
            &#10024; {{ config('app.name', 'DetergentShop') }}
        </a>
        <div class="auth-split__tagline">
            <h2 class="auth-split__heading">{{ __('auth.register_heading') }}</h2>
            <p class="auth-split__sub">{{ __('auth.register_sub') }}</p>
        </div>
        <ul class="auth-perks">
            @foreach([__('auth.register_perk_1'), __('auth.register_perk_2'), __('auth.register_perk_3')] as $perk)
            <li class="auth-perk">
                <span class="auth-perk__icon">&#10003;</span>
                {{ $perk }}
            </li>
            @endforeach
        </ul>
        <div class="auth-promo">
            <div class="auth-promo__badge">{{ __('auth.welcome_offer') }}</div>
            <div class="auth-promo__text">{!! __('auth.welcome_offer_text') !!}</div>
        </div>
    </div>

    {{-- Right panel ─────────────────────────────────────── --}}
    <div class="auth-split__right">
        <div class="auth-form-wrap">
            <div class="auth-form-header">
                <h1 class="auth-form-title">{{ __('auth.register_title') }}</h1>
                <p class="auth-form-sub">{{ __('auth.register_subtitle') }}</p>
            </div>

            <form wire:submit="register" autocomplete="off">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="auth-field">
                            <label class="auth-label" for="first_name">{{ __('auth.first_name') }}</label>
                            <input wire:model="first_name" id="first_name" type="text"
                                   class="auth-input @error('first_name') is-invalid @enderror"
                                   placeholder="John" required autofocus />
                            @error('first_name')
                                <div class="auth-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="auth-field">
                            <label class="auth-label" for="last_name">{{ __('auth.last_name') }}</label>
                            <input wire:model="last_name" id="last_name" type="text"
                                   class="auth-input @error('last_name') is-invalid @enderror"
                                   placeholder="Doe" required />
                            @error('last_name')
                                <div class="auth-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="auth-field">
                    <label class="auth-label" for="email">{{ __('auth.email') }}</label>
                    <input wire:model="email" id="email" type="email"
                           class="auth-input @error('email') is-invalid @enderror"
                           placeholder="your@email.com" required />
                    @error('email')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-2">
                    <div class="col-5">
                        <div class="auth-field">
                            <label class="auth-label" for="phone_code">{{ __('auth.country_code') }}</label>
                            <select wire:model="phone_code" id="phone_code"
                                    class="auth-input @error('phone_code') is-invalid @enderror" required>
                                <option value="">{{ __('auth.select') }}</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->phone_code }}">
                                        {{ $country->iso2 }} +{{ $country->phone_code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('phone_code')
                                <div class="auth-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="auth-field">
                            <label class="auth-label" for="phone">{{ __('auth.phone') }}</label>
                            <input wire:model="phone" id="phone" type="tel"
                                   class="auth-input @error('phone') is-invalid @enderror"
                                   placeholder="501234567" required />
                            @error('phone')
                                <div class="auth-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="auth-field">
                            <label class="auth-label" for="password">{{ __('auth.password') }}</label>
                            <input wire:model="password" id="password" type="password"
                                   class="auth-input @error('password') is-invalid @enderror"
                                   placeholder="••••••••" required />
                            @error('password')
                                <div class="auth-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="auth-field">
                            <label class="auth-label" for="password_confirmation">{{ __('auth.confirm_password') }}</label>
                            <input wire:model="password_confirmation" id="password_confirmation"
                                   type="password"
                                   class="auth-input @error('password_confirmation') is-invalid @enderror"
                                   placeholder="••••••••" required />
                            @error('password_confirmation')
                                <div class="auth-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-auth-submit">
                    {{ __('auth.create_account') }}
                </button>

                <p class="auth-terms">
                    {{ __('auth.terms_text') }}
                    <a href="#">{{ __('auth.terms_link') }}</a> {{ __('auth.and') }} <a href="#">{{ __('auth.privacy_link') }}</a>.
                </p>
            </form>

            <div class="auth-divider"><span>{{ __('auth.or') }}</span></div>

            <p class="auth-switch">
                {{ __('auth.have_account') }}
                <a href="{{ lroute('customer.login') }}" wire:navigate class="auth-switch__link">
                    {{ __('auth.sign_in_link') }}
                </a>
            </p>
        </div>
    </div>

</div>
