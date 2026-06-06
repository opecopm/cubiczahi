<div>
    @php
        $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        $primaryRole = $user->getRoleNames()->first() ?? 'No Role Assigned';
        $allRoles = implode(', ', $user->getRoleNames()->toArray()) ?: 'No Roles';
        $defaultCompany = optional($user->defaultCompany())->name ?? 'Not Set';
        $emailVerified = $user->email_verified_at ? $user->email_verified_at->format('Y-m-d') : 'Not Verified';
        $linkedProfileType = $customer ? 'Customer' : 'Unlinked';
    @endphp

    @component('admin.partials.page.inner-header', [
        'title' => $fullName,
        'breadcrumbs' => [
            [
                'label' => 'Users',
                'url' => route('admin.iam.users.index'),
                'icon' => 'back',
            ],
            [
                'label' => $fullName,
                'active' => true,
            ],
        ],
        'actionItems' => [
            [
                'title' => 'Edit',
                'route' => 'admin.iam.users.edit',
                'params' => $user->id,
                'icon' => 'ti ti-edit',
                'class' => 'btn btn-success',
            ],
            [
                'title' => 'Back',
                'route' => 'admin.iam.users.index',
                'icon' => 'ti ti-arrow-left',
                'class' => 'btn btn-outline-secondary',
            ],
        ],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible mb-3">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-3">
                {{-- User Profile Card --}}
                <div class="col-12 col-xl-8">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="avatar avatar-xl rounded">
                                            <img src="{{ $user->getFirstMediaUrl('avatars') ?: url('assets/img/no-photo.jpg') }}" alt="{{ $fullName }}">
                                        </span>
                                        <div>
                                            <span class="badge bg-info-lt mb-1">{{ $primaryRole }}</span>
                                            <h3 class="mb-1">{{ $fullName }}</h3>
                                            <div class="text-secondary">{{ $user->email }}</div>
                                            <div class="d-flex flex-wrap gap-1 mt-2">
                                                <span class="badge bg-secondary-lt">Linked: {{ $linkedProfileType }}</span>
                                                <span class="badge bg-secondary-lt">Company: {{ $defaultCompany }}</span>
                                                <span class="badge bg-secondary-lt">Menu: {{ optional($user->menu)->name ?? 'Not Set' }}</span>
                                                <span class="badge {{ $user->email_verified_at ? 'bg-success-lt' : 'bg-warning-lt' }}">
                                                    Email: {{ $emailVerified }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 mt-3 mt-lg-0">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="card card-sm text-center">
                                                <div class="card-body p-2">
                                                    <div class="text-secondary text-uppercase text-muted small mb-1">Companies</div>
                                                    <div class="h3 mb-0">{{ $user->companies->count() }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card card-sm text-center">
                                                <div class="card-body p-2">
                                                    <div class="text-secondary text-uppercase text-muted small mb-1">Roles</div>
                                                    <div class="h3 mb-0">{{ count($user->getRoleNames()) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card card-sm text-center">
                                                <div class="card-body p-2">
                                                    <div class="text-secondary text-uppercase text-muted small mb-1">Status</div>
                                                    <div class="h5 mb-0">{{ $user->email_verified_at ? 'Verified' : 'Pending' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Navigation Card --}}
                <div class="col-12 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Quick Navigation</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Jump to User</label>
                                <select class="form-select" wire:model.live="userId">
                                    <option value="{{ $user->id }}">{{ $fullName }}</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}" @if ($u->id == $user->id) selected @endif>
                                            {{ $u->first_name . ' ' . $u->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="subheader mb-2">Profile Summary</div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-secondary">Primary Role</span>
                                    <span class="fw-bold">{{ $primaryRole }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-secondary">Default Company</span>
                                    <span class="fw-bold text-end">{{ $defaultCompany }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-secondary">Companies</span>
                                    <span class="fw-bold">{{ $user->companies->count() }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- User Information --}}
                <div class="col-12 col-lg-7">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">User Information</h3>
                            <div class="card-options">
                                @can('update_users')
                                    <button type="button" class="btn btn-sm btn-primary" wire:click="toggleRoleModal">Add Role</button>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Email</div>
                                    <div class="datagrid-content">{{ $user->email }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Primary Role</div>
                                    <div class="datagrid-content">{{ $primaryRole }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Menu</div>
                                    <div class="datagrid-content">{{ optional($user->menu)->name ?? 'Not Assigned' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Email Verified</div>
                                    <div class="datagrid-content">{{ $emailVerified }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Default Company</div>
                                    <div class="datagrid-content">{{ $defaultCompany }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Companies Count</div>
                                    <div class="datagrid-content">{{ $user->companies->count() }}</div>
                                </div>
                                <div class="datagrid-item" style="grid-column: 1 / -1;">
                                    <div class="datagrid-title">All Roles</div>
                                    <div class="datagrid-content">
                                        <div class="d-flex flex-wrap gap-1">
                                            @forelse ($user->getRoleNames() as $roleName)
                                                <span class="badge bg-secondary-lt d-flex align-items-center gap-1">
                                                    {{ $roleName }}
                                                    @can('update_users')
                                                        <button type="button" wire:click="removeRole('{{ $roleName }}')" class="btn-close btn-close-sm ms-1" style="font-size:.5rem;" title="Remove Role"></button>
                                                    @endcan
                                                </span>
                                            @empty
                                                <span class="text-secondary">No Roles</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Account Overview --}}
                <div class="col-12 col-lg-5">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Account Overview</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center px-0">
                                    <span class="avatar avatar-sm bg-info-lt me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M9 12l2 2l5 -5"/><path d="M17 3l0 3"/><path d="M17 20l0 1"/></svg>
                                    </span>
                                    <div>
                                        <div class="fw-bold small">Current Role</div>
                                        <div class="text-secondary small">{{ $primaryRole }}</div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex align-items-center px-0">
                                    <span class="avatar avatar-sm bg-success-lt me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0"/><path d="M9 8l1 0"/><path d="M9 12l1 0"/><path d="M9 16l1 0"/><path d="M14 8l1 0"/><path d="M14 12l1 0"/><path d="M14 16l1 0"/><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16"/></svg>
                                    </span>
                                    <div>
                                        <div class="fw-bold small">Default Company</div>
                                        <div class="text-secondary small">{{ $defaultCompany }}</div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex align-items-center px-0">
                                    <span class="avatar avatar-sm bg-secondary-lt me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6l16 0"/><path d="M4 12l16 0"/><path d="M4 18l16 0"/></svg>
                                    </span>
                                    <div>
                                        <div class="fw-bold small">Menu Access</div>
                                        <div class="text-secondary small">{{ optional($user->menu)->name ?? 'No Menu Assigned' }}</div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex align-items-center px-0">
                                    <span class="avatar avatar-sm bg-warning-lt me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                                    </span>
                                    <div>
                                        <div class="fw-bold small">Verification</div>
                                        <div class="text-secondary small">{{ $user->email_verified_at ? 'Email verified successfully.' : 'Email verification pending.' }}</div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Associated Profile Details --}}
                <div class="col-12 col-lg-7">
                    <div class="card h-100">
                        <div class="card-header">
                            <div>
                                <h3 class="card-title">Associated Profile Details</h3>
                                <div class="card-subtitle">Linked {{ strtolower($linkedProfileType) }} record for this user.</div>
                            </div>
                            <div class="card-options">
                                @if($customer)
                                    <a href="{{ route('crm.customers.show', $customer->id) }}" class="btn btn-sm btn-outline-info">View Customer</a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if($customer)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="datagrid">
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Reference</div>
                                                <div class="datagrid-content">{{ $customer->reference ?? '-' }}</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Name</div>
                                                <div class="datagrid-content">{{ $customer->name ?? '-' }}</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Company</div>
                                                <div class="datagrid-content">{{ $customer->company ?? '-' }}</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Customer Group</div>
                                                <div class="datagrid-content">{{ optional($customer->customerGroup)->name ?? 'Not Assigned' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="datagrid">
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Email</div>
                                                <div class="datagrid-content">{{ $customer->email ?? '-' }}</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Phone</div>
                                                <div class="datagrid-content">{{ $customer->phone ?? '-' }}</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Industry</div>
                                                <div class="datagrid-content">{{ $customer->industry ?? '-' }}</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Status</div>
                                                <div class="datagrid-content">{{ ucfirst($customer->status ?? '-') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="mb-3 text-secondary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="48" height="48" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4"/><path d="M17 17l5 5"/><path d="M22 17l-5 5"/></svg>
                                    </div>
                                    <h5>No Linked Profile</h5>
                                    <p class="text-secondary">This user is not currently linked to a customer record.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Linked Profile Contacts --}}
                <div class="col-12 col-lg-5">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Linked Profile Contacts</h3>
                        </div>
                        <div class="card-body">
                            @if($customer)
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-secondary">Customer Email</span>
                                        <span class="fw-bold text-end">{{ $customer->email ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-secondary">Phone</span>
                                        <span class="fw-bold text-end">{{ $customer->phone ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-secondary">Website</span>
                                        <span class="fw-bold text-end">{{ $customer->website ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-secondary">Company</span>
                                        <span class="fw-bold text-end">{{ $customer->company ?? '-' }}</span>
                                    </li>
                                </ul>
                            @else
                                <p class="text-secondary">Contact details will appear here once this user is linked to a customer profile.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- User Companies --}}
                <div class="col-12 col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <div>
                                <h3 class="card-title">User Companies</h3>
                                <div class="card-subtitle">Manage company assignments for this user.</div>
                            </div>
                            <div class="card-options">
                                <button wire:click="toggleCompanyModal" class="btn btn-sm btn-primary">Assign Company</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Default</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($user->companies as $company)
                                        <tr>
                                            <td class="fw-bold">{{ $company->name }}</td>
                                            <td>
                                                @if(optional($user->defaultCompany())->id === $company->id)
                                                    <span class="badge bg-success-lt">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary-lt">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @can('revoke_user_company')
                                                    <button wire:click="confirmDelete('company', {{ $company->id }})" class="btn btn-sm btn-ghost-danger">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                                    </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-secondary py-4">No companies assigned.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- User Locations --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3 class="card-title">User Locations</h3>
                                <div class="card-subtitle">Manage assigned locations for this user.</div>
                            </div>
                            <div class="card-options">
                                <button wire:click="toggleLocationModal" class="btn btn-sm btn-primary">Assign Location</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($user->locations as $location)
                                        <tr>
                                            <td class="fw-bold">{{ $location->code ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('admin.business.locations.show', $location->id) }}">
                                                    {{ $location->name }}
                                                </a>
                                            </td>
                                            <td class="text-secondary">{{ \Modules\Business\Models\Location::TYPE_SELECT[$location->type] ?? ucfirst($location->type ?? '-') }}</td>
                                            <td>
                                                @can('revoke_user_location')
                                                    <button wire:click="confirmDelete('location', {{ $location->id }})" class="btn btn-sm btn-ghost-danger">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                                    </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-secondary py-4">No locations assigned.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Company Assignment Modal --}}
    <div class="modal modal-blur fade @if($showCompanyModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showCompanyModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Company</h5>
                    <button type="button" class="btn-close" wire:click="toggleCompanyModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Company</label>
                        <select class="form-select" wire:model="selectedCompanyId">
                            <option value="">Choose a company...</option>
                            @foreach($availableCompanies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedCompanyId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="isDefaultCompany" wire:model="isDefaultCompany">
                        <label class="form-check-label" for="isDefaultCompany">Set as Default Company</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="toggleCompanyModal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="assignCompany">Assign</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Role Assignment Modal --}}
    <div class="modal modal-blur fade @if($showRoleModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showRoleModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Additional Role</h5>
                    <button type="button" class="btn-close" wire:click="toggleRoleModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Role</label>
                        <select class="form-select" wire:model="selectedRoleName">
                            <option value="">Choose a role...</option>
                            @foreach($availableRoles as $r)
                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedRoleName') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="toggleRoleModal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="assignAdditionalRole">Assign</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Location Assignment Modal --}}
    <div class="modal modal-blur fade @if($showLocationModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showLocationModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Location</h5>
                    <button type="button" class="btn-close" wire:click="toggleLocationModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Location</label>
                        <select class="form-select" wire:model="selectedLocationId">
                            <option value="">Choose a location...</option>
                            @foreach($availableLocations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }} ({{ $location->code ?? '-' }})</option>
                            @endforeach
                        </select>
                        @error('selectedLocationId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="toggleLocationModal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="assignLocation">Assign</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal modal-blur fade @if($showDeleteModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showDeleteModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-title">Are you sure?</div>
                    <div>Do you really want to remove this {{ $deleteType }}?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="cancelDelete">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="executeDelete">Yes, remove</button>
                </div>
            </div>
        </div>
    </div>
</div>
