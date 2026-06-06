<div>
    {{-- Page header --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-user-circle me-2 text-primary"></i>My Profile
                    </h2>
                    <div class="text-muted mt-1">Manage your account details and security settings.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row g-4">

                {{-- ── Left: Avatar + identity card ── --}}
                <div class="col-lg-3">
                    <div class="card text-center">
                        <div class="card-body py-4">

                            {{-- Avatar --}}
                            <span class="avatar avatar-xl rounded-circle mx-auto mb-3 fs-1 fw-bold text-white"
                                  style="background: linear-gradient(135deg, #206bc4 0%, #4dabf7 100%); width:80px; height:80px; font-size:2rem;">
                                {{ auth()->user()->initials() }}
                            </span>

                            <h3 class="mb-0">{{ auth()->user()->name }}</h3>
                            <div class="text-muted small mt-1">{{ auth()->user()->email }}</div>

                            @if(auth()->user()->phone)
                                <div class="text-muted small mt-1">
                                    <i class="ti ti-phone me-1"></i>{{ auth()->user()->phone_code }} {{ auth()->user()->phone }}
                                </div>
                            @endif

                            <div class="mt-3">
                                <span class="badge bg-primary-lt">
                                    {{ ucfirst(auth()->user()->type ?? 'Admin') }}
                                </span>
                                @if($mfa_enabled)
                                    <span class="badge bg-green-lt ms-1">
                                        <i class="ti ti-shield-check me-1"></i>2FA On
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Stat strip --}}
                        <div class="card-footer p-0">
                            <div class="row g-0 text-center divide-x">
                                <div class="col py-3">
                                    <div class="fw-bold">{{ \Modules\Selling\Models\SalesOrder::where('created_by', auth()->id())->count() }}</div>
                                    <div class="text-muted" style="font-size:.7rem;">Orders</div>
                                </div>
                                <div class="col py-3">
                                    <div class="fw-bold">{{ auth()->user()->roles->count() }}</div>
                                    <div class="text-muted" style="font-size:.7rem;">Roles</div>
                                </div>
                                <div class="col py-3">
                                    <div class="fw-bold">{{ auth()->user()->created_at->diffForHumans(null, true) }}</div>
                                    <div class="text-muted" style="font-size:.7rem;">Member</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab nav --}}
                    <div class="list-group mt-3">
                        <button type="button" wire:click="$set('activeTab','profile')"
                            class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ $activeTab === 'profile' ? 'active' : '' }}">
                            <i class="ti ti-user"></i> Profile Info
                        </button>
                        <button type="button" wire:click="$set('activeTab','password')"
                            class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ $activeTab === 'password' ? 'active' : '' }}">
                            <i class="ti ti-lock"></i> Change Password
                        </button>
                        <button type="button" wire:click="$set('activeTab','security')"
                            class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ $activeTab === 'security' ? 'active' : '' }}">
                            <i class="ti ti-shield"></i> Security
                        </button>
                    </div>
                </div>

                {{-- ── Right: Tab content ── --}}
                <div class="col-lg-9">

                    {{-- ──────────────── Profile Info ──────────────── --}}
                    @if($activeTab === 'profile')
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ti ti-user me-2"></i>Profile Information</h3>
                        </div>
                        <div class="card-body">

                            @if(session('profile_success'))
                                <div class="alert alert-success alert-dismissible mb-4" role="alert">
                                    <div class="d-flex">
                                        <i class="ti ti-circle-check me-2 mt-1"></i>
                                        <div>{{ session('profile_success') }}</div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">First Name</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                           wire:model="first_name" placeholder="John">
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                           wire:model="last_name" placeholder="Doe">
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label required">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                               wire:model="email" placeholder="you@example.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Phone Code</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-world"></i></span>
                                        <input type="text" class="form-control @error('phone_code') is-invalid @enderror"
                                               wire:model="phone_code" placeholder="+1">
                                    </div>
                                </div>

                                <div class="col-md-9">
                                    <label class="form-label">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-phone"></i></span>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                               wire:model="phone" placeholder="555 000 0000">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="button" wire:click="saveProfile"
                                    class="btn btn-primary"
                                    wire:loading.attr="disabled">
                                <span wire:loading wire:target="saveProfile" class="spinner-border spinner-border-sm me-1"></span>
                                <i wire:loading.remove wire:target="saveProfile" class="ti ti-device-floppy me-1"></i>
                                Save Changes
                            </button>
                        </div>
                    </div>
                    @endif

                    {{-- ──────────────── Change Password ──────────────── --}}
                    @if($activeTab === 'password')
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ti ti-lock me-2"></i>Change Password</h3>
                        </div>
                        <div class="card-body">

                            @if(session('password_success'))
                                <div class="alert alert-success alert-dismissible mb-4" role="alert">
                                    <div class="d-flex">
                                        <i class="ti ti-circle-check me-2 mt-1"></i>
                                        <div>{{ session('password_success') }}</div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <div class="row g-3" style="max-width: 520px;">
                                <div class="col-12">
                                    <label class="form-label required">Current Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                        <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                               wire:model="current_password" placeholder="••••••••" autocomplete="current-password">
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label required">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-key"></i></span>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                               wire:model="password" placeholder="••••••••" autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label required">Confirm New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-key"></i></span>
                                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                               wire:model="password_confirmation" placeholder="••••••••" autocomplete="new-password">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="text-muted small">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Password must be at least 8 characters and include a mix of letters and numbers.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="button" wire:click="updatePassword"
                                    class="btn btn-primary"
                                    wire:loading.attr="disabled">
                                <span wire:loading wire:target="updatePassword" class="spinner-border spinner-border-sm me-1"></span>
                                <i wire:loading.remove wire:target="updatePassword" class="ti ti-lock-check me-1"></i>
                                Update Password
                            </button>
                        </div>
                    </div>
                    @endif

                    {{-- ──────────────── Security ──────────────── --}}
                    @if($activeTab === 'security')
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ti ti-shield me-2"></i>Security Settings</h3>
                        </div>
                        <div class="card-body">

                            @if(session('mfa_success'))
                                <div class="alert alert-success alert-dismissible mb-4" role="alert">
                                    <div class="d-flex">
                                        <i class="ti ti-circle-check me-2 mt-1"></i>
                                        <div>{{ session('mfa_success') }}</div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{-- MFA toggle --}}
                            <div class="card border mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="avatar bg-{{ $mfa_enabled ? 'green' : 'secondary' }}-lt text-{{ $mfa_enabled ? 'green' : 'secondary' }}">
                                                    <i class="ti ti-shield-lock fs-4"></i>
                                                </span>
                                                <div>
                                                    <div class="fw-medium">Two-Factor Authentication (2FA)</div>
                                                    <div class="text-muted small">Add an extra layer of security to your account.</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <label class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox"
                                                       wire:model.live="mfa_enabled"
                                                       role="switch">
                                                <span class="form-check-label fw-medium {{ $mfa_enabled ? 'text-green' : 'text-muted' }}">
                                                    {{ $mfa_enabled ? 'Enabled' : 'Disabled' }}
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Session info --}}
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="avatar bg-blue-lt text-blue">
                                            <i class="ti ti-device-desktop fs-4"></i>
                                        </span>
                                        <div class="flex-fill">
                                            <div class="fw-medium">Current Session</div>
                                            <div class="text-muted small">
                                                IP: {{ request()->ip() }} &mdash; Last active now
                                            </div>
                                        </div>
                                        <span class="badge bg-green-lt text-green">
                                            <i class="ti ti-circle-filled me-1" style="font-size:.5rem;"></i>Active
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Account info --}}
                            <div class="mt-3 p-3 rounded-2 bg-light">
                                <div class="row g-2 small text-muted">
                                    <div class="col-sm-6 d-flex gap-2">
                                        <i class="ti ti-calendar-event flex-shrink-0 mt-1"></i>
                                        <div>
                                            <div class="fw-medium text-body">Member since</div>
                                            {{ auth()->user()->created_at->format('d M Y') }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6 d-flex gap-2">
                                        <i class="ti ti-refresh flex-shrink-0 mt-1"></i>
                                        <div>
                                            <div class="fw-medium text-body">Last updated</div>
                                            {{ auth()->user()->updated_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6 d-flex gap-2">
                                        <i class="ti ti-mail flex-shrink-0 mt-1"></i>
                                        <div>
                                            <div class="fw-medium text-body">Email verified</div>
                                            {{ auth()->user()->email_verified_at ? auth()->user()->email_verified_at->format('d M Y') : 'Not verified' }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6 d-flex gap-2">
                                        <i class="ti ti-user-check flex-shrink-0 mt-1"></i>
                                        <div>
                                            <div class="fw-medium text-body">Account status</div>
                                            <span class="badge bg-{{ auth()->user()->status === 'active' ? 'green' : 'danger' }}-lt">
                                                {{ ucfirst(auth()->user()->status ?? 'active') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
