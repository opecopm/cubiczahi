<div>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-4">{{ __('Admin Login') }}</h2>

            <x-admin.auth-session-status class="mb-3" :status="session('status')" />

            <form wire:submit="login" autocomplete="off">
                <div class="mb-3">
                    <x-admin.input-label for="email" :value="__('Email address')" />
                    <x-admin.text-input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username" placeholder="your@email.com" />
                    <x-admin.input-error :messages="$errors->get('form.email')" />
                </div>

                <div class="mb-2">
                    <x-admin.input-label for="password" :value="__('Password')">
                        @if (Route::has('admin.password.request'))
                            <span class="form-label-description">
                                <a href="{{ route('admin.password.request') }}" wire:navigate>{{ __('Forgot password?') }}</a>
                            </span>
                        @endif
                    </x-admin.input-label>
                    <x-admin.text-input wire:model="form.password" id="password" type="password" name="password" required autocomplete="current-password" placeholder="Your password" />
                    <x-admin.input-error :messages="$errors->get('form.password')" />
                </div>

                <div class="mb-3">
                    <label class="form-check">
                        <input wire:model="form.remember" id="remember" type="checkbox" class="form-check-input" name="remember" />
                        <span class="form-check-label">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="form-footer">
                    <x-admin.primary-button class="w-100">
                        {{ __('Sign in') }}
                    </x-admin.primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
