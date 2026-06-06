<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">{{ __('Reset password') }}</h2>

        <form wire:submit="resetPassword">
            <input type="hidden" wire:model="token" />

            <div class="mb-3">
                <x-admin.input-label for="email" :value="__('Email address')" />
                <x-admin.text-input wire:model="email" id="email" type="email" name="email" required autofocus autocomplete="username" placeholder="your@email.com" />
                <x-admin.input-error :messages="$errors->get('email')" />
            </div>

            <div class="mb-3">
                <x-admin.input-label for="password" :value="__('New Password')" />
                <x-admin.text-input wire:model="password" id="password" type="password" name="password" required autocomplete="new-password" placeholder="New password" />
                <x-admin.input-error :messages="$errors->get('password')" />
            </div>

            <div class="mb-3">
                <x-admin.input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-admin.text-input wire:model="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm new password" />
                <x-admin.input-error :messages="$errors->get('password_confirmation')" />
            </div>

            <div class="form-footer">
                <x-admin.primary-button class="w-100">
                    {{ __('Reset Password') }}
                </x-admin.primary-button>
            </div>
        </form>
    </div>
</div>
