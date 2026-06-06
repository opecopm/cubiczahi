<div>
    <div class="row g-4">
        <div class="col-lg-7">
            <form wire:submit="updatePassword" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.05), rgba(10, 36, 99, 0.05)); border-radius: 12px; padding: 24px;">
                <h5 style="font-weight: 700; color: #0a2463; margin-bottom: 6px;">Update Password</h5>
                <p class="text-muted mb-4">Use a strong password you do not use anywhere else.</p>

                @if(session('password_success'))
                    <div class="alert alert-success" style="border-radius: 8px; border: none;">{{ session('password_success') }}</div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-semibold">Current Password</label>
                    <input type="password" wire:model="current_password" class="form-control @error('current_password') is-invalid @enderror"
                           autocomplete="current-password" style="border-radius: 8px; border: 2px solid #e5e7eb; padding: 10px 14px;">
                    @error('current_password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">New Password</label>
                    <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror"
                           autocomplete="new-password" style="border-radius: 8px; border: 2px solid #e5e7eb; padding: 10px 14px;">
                    @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Confirm New Password</label>
                    <input type="password" wire:model="password_confirmation" class="form-control"
                           autocomplete="new-password" style="border-radius: 8px; border: 2px solid #e5e7eb; padding: 10px 14px;">
                </div>

                <button type="submit" wire:loading.attr="disabled" class="btn btn-primary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                    <span wire:loading.remove>Save Password</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Saving...
                    </span>
                </button>
            </form>
        </div>

        <div class="col-lg-5">
            <div style="background: #f8f9fa; border-radius: 12px; padding: 24px; border: 1px solid #e5e7eb;">
                <div class="d-flex justify-content-between gap-3 align-items-start mb-3">
                    <div>
                        <h5 style="font-weight: 700; color: #0a2463; margin-bottom: 6px;">Multi-Factor Authentication</h5>
                        <p class="text-muted mb-0">Require an extra verification step when signing in.</p>
                    </div>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="mfa_enabled" wire:model.live="mfa_enabled">
                    </div>
                </div>

                @if(session('mfa_success'))
                    <div class="alert alert-success mb-3" style="border-radius: 8px; border: none;">{{ session('mfa_success') }}</div>
                @endif

                <div style="background: {{ $mfa_enabled ? '#d4edda' : '#fff3cd' }}; color: {{ $mfa_enabled ? '#155724' : '#856404' }}; border-radius: 8px; padding: 12px 14px; font-weight: 600;">
                    MFA is {{ $mfa_enabled ? 'enabled' : 'disabled' }}
                </div>

                <div class="text-muted mt-3" style="font-size: 0.9rem; line-height: 1.6;">
                    This switch stores your MFA preference. Your existing OTP fields can be used by the login flow to challenge users when this setting is enabled.
                </div>
            </div>
        </div>
    </div>
</div>
