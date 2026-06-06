<div class="auth-split">

    {{-- Left panel ─────────────────────────────────────── --}}
    <div class="auth-split__left">
        <a href="{{ route('home') }}" wire:navigate class="auth-brand">
            &#9910; {{ config('app.name', 'LaundryPro') }}
        </a>
        <div class="auth-split__tagline">
            <h2 class="auth-split__heading">Join thousands of happy customers.</h2>
            <p class="auth-split__sub">Create your free account and get your first order 20% off.</p>
        </div>
        <ul class="auth-perks">
            @foreach(['No subscription required','Cancel anytime','Free first pickup'] as $perk)
            <li class="auth-perk">
                <span class="auth-perk__icon">&#10003;</span>
                {{ $perk }}
            </li>
            @endforeach
        </ul>
        <div class="auth-promo">
            <div class="auth-promo__badge">&#127881; Limited Offer</div>
            <div class="auth-promo__text">Sign up today and get <strong>20% off</strong> your first order</div>
        </div>
    </div>

    {{-- Right panel ─────────────────────────────────────── --}}
    <div class="auth-split__right">
        <div class="auth-form-wrap">
            <div class="auth-form-header">
                <h1 class="auth-form-title">Create your account</h1>
                <p class="auth-form-sub">It's free and only takes a minute</p>
            </div>

            <form wire:submit="register" autocomplete="off">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="auth-field">
                            <label class="auth-label" for="first_name">First name</label>
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
                            <label class="auth-label" for="last_name">Last name</label>
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
                    <label class="auth-label" for="email">Email address</label>
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
                            <label class="auth-label" for="phone_code">Country code</label>
                            <select wire:model="phone_code" id="phone_code"
                                    class="auth-input @error('phone_code') is-invalid @enderror" required>
                                <option value="">— Select —</option>
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
                            <label class="auth-label" for="phone">Phone number</label>
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
                            <label class="auth-label" for="password">Password</label>
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
                            <label class="auth-label" for="password_confirmation">Confirm</label>
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
                    Create Free Account
                </button>

                <p class="auth-terms">
                    By creating an account you agree to our
                    <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
                </p>
            </form>

            <div class="auth-divider"><span>or</span></div>

            <p class="auth-switch">
                Already have an account?
                <a href="{{ route('customer.login') }}" wire:navigate class="auth-switch__link">
                    Sign in
                </a>
            </p>
        </div>
    </div>

</div>
