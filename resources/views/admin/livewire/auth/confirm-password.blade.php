<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">{{ __('Confirm Password') }}</h2>

        <p class="text-secondary mb-4">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>

        <form wire:submit="confirmPassword">
            <div class="mb-3">
                <x-admin.input-label for="password" :value="__('Password')" />
                <x-admin.text-input wire:model="password" id="password" type="password" name="password" required autocomplete="current-password" placeholder="Your password" />
                <x-admin.input-error :messages="$errors->get('password')" />
            </div>

            <div class="form-footer">
                <x-admin.primary-button class="w-100">
                    {{ __('Confirm') }}
                </x-admin.primary-button>
            </div>
        </form>
    </div>
</div>
