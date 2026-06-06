<div>
    <div class="card card-md">
        <div class="card-body text-center py-4">

            {{-- Icon --}}
            <span class="avatar avatar-lg rounded-circle mb-3"
                  style="background: linear-gradient(135deg,#206bc4 0%,#4dabf7 100%);">
                <i class="ti ti-shield-check text-white" style="font-size:1.8rem;"></i>
            </span>

            <h2 class="h2 mb-1">Two-Factor Authentication</h2>
            <p class="text-muted mb-4">
                We sent a 6-digit code to
                <strong>{{ $email ?: 'your email address' }}</strong>
                <br>Enter it below to continue.
            </p>

            {{-- Success resend notice --}}
            @if($resent)
                <div class="alert alert-success py-2 mb-3 text-start">
                    <i class="ti ti-circle-check me-1"></i> A new code was sent.
                </div>
            @endif

            <form wire:submit="verify">
                <div class="mb-3">
                    <input
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="6"
                        wire:model="otp"
                        autofocus
                        placeholder="000000"
                        class="form-control form-control-lg text-center @error('otp') is-invalid @enderror"
                        style="font-size:2rem; letter-spacing:.5rem; font-weight:700;"
                    >
                    @error('otp')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-footer">
                    <button type="submit"
                            class="btn btn-primary w-100"
                            wire:loading.attr="disabled">
                        <span wire:loading wire:target="verify" class="spinner-border spinner-border-sm me-1"></span>
                        <i wire:loading.remove wire:target="verify" class="ti ti-lock-open me-1"></i>
                        Verify &amp; Sign In
                    </button>
                </div>
            </form>

            <div class="mt-4 text-muted small">
                Didn't receive a code?
                <button type="button"
                        wire:click="resend"
                        wire:loading.attr="disabled"
                        class="btn btn-link btn-sm p-0 text-primary">
                    <span wire:loading wire:target="resend">Sending…</span>
                    <span wire:loading.remove wire:target="resend">Resend code</span>
                </button>
            </div>

            <div class="mt-2">
                <a href="{{ route('admin.login') }}" wire:navigate class="text-muted small">
                    <i class="ti ti-arrow-left me-1"></i>Back to login
                </a>
            </div>

        </div>
    </div>
</div>
