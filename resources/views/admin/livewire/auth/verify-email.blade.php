<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">{{ __('Verify Email') }}</h2>

        <p class="text-secondary mb-4">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success mb-4">
                {{ __('A new verification link has been sent to your email address.') }}
            </div>
        @endif

        <div class="form-footer">
            <x-admin.primary-button wire:click="sendVerification" class="w-100">
                {{ __('Resend Verification Email') }}
            </x-admin.primary-button>
        </div>

        <div class="text-center text-secondary mt-3">
            <button wire:click="logout" class="btn btn-link text-secondary">
                {{ __('Log Out') }}
            </button>
        </div>
    </div>
</div>
