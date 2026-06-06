<div>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-4">{{ __('Forgot password') }}</h2>

            <p class="text-secondary mb-4">
                {{ __('Enter your email address and we\'ll send you a link to reset your password.') }}
            </p>

            <x-admin.auth-session-status class="mb-3" :status="session('status')" />

            <form wire:submit="sendPasswordResetLink">
                <div class="mb-3">
                    <x-admin.input-label for="email" :value="__('Email address')" />
                    <x-admin.text-input wire:model="email" id="email" type="email" name="email" required autofocus placeholder="your@email.com" />
                    <x-admin.input-error :messages="$errors->get('email')" />
                </div>

                <div class="form-footer">
                    <x-admin.primary-button class="w-100">
                        {{ __('Send Reset Link') }}
                    </x-admin.primary-button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center text-secondary mt-3">
        <a href="{{ route('admin.login') }}" wire:navigate>{{ __('Back to login') }}</a>
    </div>
</div>
