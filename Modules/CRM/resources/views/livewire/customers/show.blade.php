<div>
    @component('admin.partials.page.inner-header', [
        'title' => $customer->name,
        'breadcrumbs' => [
            [
                'label' => 'Customers',
                'url' => route('admin.crm.customers.index'),
                'icon' => 'back',
            ],
            [
                'label' => $customer->name,
                'url' => route('admin.crm.customers.show', $customer->id),
                'class' => 'text-body fw-medium',
            ],
            [
                'label' => 'Profile',
                'active' => true,
            ],
        ],
        'actionItems' => [
            [
                'type' => 'badge',
                'title' => ucfirst($customer->status),
                'class' => $customer->status == 'active' ? 'bg-success-lt' : 'bg-secondary-lt',
            ],
            [
                'title' => 'Orders',
                'route' => 'admin.crm.customers.orders',
                'params' => ['customer' => $customer->id],
                'icon' => 'ti ti-list',
                'class' => 'btn btn-sm btn-primary',
            ],
            [
                'title' => 'Invoices',
                'route' => 'admin.crm.customers.invoices',
                'params' => ['customer' => $customer->id],
                'icon' => 'ti ti-file-invoice',
                'class' => 'btn btn-sm btn-primary',
            ],
            [
                'title' => 'Edit Customer',
                'route' => 'admin.crm.customers.edit',
                'params' => ['customer' => $customer->id],
                'icon' => 'ti ti-edit',
                'class' => 'btn btn-sm btn-success',
            ],
        ],
    ])
    @if($customer->email || $customer->phone)
        @slot('meta')
            @if($customer->email)
                <span class="me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7l9 6l9 -6l-18 0v13c0 0.552 0.448 1 1 1h16c0.552 0 1 -0.448 1 -1v-13l-18 0"/></svg>
                    {{ $customer->email }}
                </span>
            @endif
            @if($customer->phone)
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l2 5l-1.5 1.5a11 11 0 0 0 5 5l1.5 -1.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"/></svg>
                    {{ $customer->phone }}
                </span>
            @endif
        @endslot
    @endif

    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            @php
                $secondaryLang = system_setting('secondary_language', 'ar');
                $companyEn = $customer->getTranslation('company', 'en');
                $companySecondary = $customer->getTranslation('company', $secondaryLang);
                $customerInitial = strtoupper(mb_substr($customer->name ?? '?', 0, 1));

                $crDoc = $customer->generalDocuments
                    ->where('type', 'cr')
                    ->where('status', 'active')
                    ->sortByDesc('created_at')
                    ->first();
                $trnDoc = $customer->generalDocuments
                    ->where('type', 'trn')
                    ->where('status', 'active')
                    ->sortByDesc('created_at')
                    ->first();
            @endphp

            <div class="row mt-3">
                <div class="col-12">
                    <div class="row">
                        <div class="col-12 col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header p-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Profile</h5>
                                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="resetStatus" data-bs-toggle="modal" data-bs-target="#update_status_modal">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                        Update Status
                                    </button>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Reference</span>
                                            <span class="text-sm font-weight-bold">{{ $customer->reference ?? $customer->id }}</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Status</span>
                                            <span class="badge {{ $customer->status === 'active' ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ ucfirst($customer->status) }}</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Customer Group</span>
                                            <span class="text-sm font-weight-bold">{{ $customer->customerGroup->name ?? '—' }}</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Email</span>
                                            <span class="text-sm font-weight-bold">{{ $customer->email ?: '—' }}</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Phone</span>
                                            <span class="text-sm font-weight-bold">{{ trim(($customer->phone_code ? ('+' . $customer->phone_code . ' ') : '') . ($customer->phone ?? '')) ?: '—' }}</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Industry</span>
                                            <span class="text-sm font-weight-bold">{{ $customer->industry ?: '—' }}</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Company (EN)</span>
                                            <span class="text-sm font-weight-bold">{{ $companyEn ?: '—' }}</span>
                                        </div>
                                        @if ($secondaryLang !== 'en')
                                            <div class="col-md-6 mb-3">
                                                <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Company ({{ strtoupper($secondaryLang) }})</span>
                                                <span class="text-sm font-weight-bold">{{ $companySecondary ?: '—' }}</span>
                                            </div>
                                        @endif
                                        <div class="col-md-6 mb-3">
                                            <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Website</span>
                                            @if($customer->website)
                                                <a href="{{ $customer->website }}" target="_blank" rel="noopener" class="text-sm font-weight-bold">{{ $customer->website }}</a>
                                            @else
                                                <span class="text-sm font-weight-bold">—</span>
                                            @endif
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">CRN</span>
                                            <span class="text-sm font-weight-bold">{{ $customer->crn ?: '—' }}</span>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">TRN</span>
                                            <span class="text-sm font-weight-bold">{{ $customer->trn ?: '—' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header p-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Addresses</h5>
                                    <button type="button" class="btn btn-sm btn-primary" wire:click="openModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                                        Add Address
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Type</th>
                                                <th>Country</th>
                                                <th>State</th>
                                                <th>City</th>
                                                <th>Address</th>
                                                <th>Zip</th>
                                                <th class="w-1">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($customer->addresses as $key => $address)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ ucwords(str_replace('_', ' ', $address->address_type)) }}</td>
                                                    <td>{{ $address->country ?: '—' }}</td>
                                                    <td>{{ $address->state ?: '—' }}</td>
                                                    <td>{{ $address->city ?: '—' }}</td>
                                                    <td>{{ trim(($address->line1 ?? '') . ' ' . ($address->line2 ?? '')) ?: '—' }}</td>
                                                    <td>{{ $address->postal_code ?: '—' }}</td>
                                                    <td class="text-end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <button type="button" class="dropdown-item" wire:click="editAddress({{ $address->id }})">Edit</button>
                                                                <button type="button" class="dropdown-item text-danger" wire:click="deleteAddress({{ $address->id }})">Delete</button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-secondary py-4">No addresses found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="card mb-4">
                                <div class="card-body p-3 text-center">
                                    <span class="avatar avatar-xl bg-primary-lt text-primary">{{ $customerInitial }}</span>
                                    <div class="mt-3">
                                        <h6 class="mb-0">{{ $customer->name }}</h6>
                                        <div class="text-xs text-muted mt-1">{{ $customer->reference ?? $customer->id }}</div>
                                        <div class="btn-list justify-content-center mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-secondary d-print-none" wire:click="resetStatus" data-bs-toggle="modal" data-bs-target="#update_status_modal">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a9 9 0 1 0 9 9"/><path d="M12 7v5l3 3"/><path d="M16 3h5v5"/></svg>
                                                Status
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-body p-3">
                                    <h6 class="text-uppercase text-info text-xs font-weight-bolder mb-3">Overview</h6>
                                    <ul class="list-group">
                                        <li class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm {{ $customer->status === 'active' ? 'bg-success-lt' : 'bg-secondary-lt' }} me-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/><path d="M12 7v5l3 3"/></svg>
                                                </span>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1 text-dark text-sm">Status</h6>
                                                    <span class="text-xs">{{ ucfirst($customer->status) }}</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm bg-info-lt me-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 7h14"/><path d="M5 12h14"/><path d="M5 17h14"/></svg>
                                                </span>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1 text-dark text-sm">Customer Group</h6>
                                                    <span class="text-xs">{{ $customer->customerGroup->name ?? '—' }}</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm bg-secondary-lt me-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7l9 6l9 -6"/><path d="M21 7l0 10a2 2 0 0 1 -2 2l-14 0a2 2 0 0 1 -2 -2l0 -10"/></svg>
                                                </span>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1 text-dark text-sm">Email</h6>
                                                    <span class="text-xs">{{ $customer->email ?: '—' }}</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm bg-warning-lt me-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l2 5l-1.5 1.5a11 11 0 0 0 5 5l1.5 -1.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"/></svg>
                                                </span>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1 text-dark text-sm">Phone</h6>
                                                    <span class="text-xs">{{ trim(($customer->phone_code ? ('+' . $customer->phone_code . ' ') : '') . ($customer->phone ?? '')) ?: '—' }}</span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>

                                    <hr class="my-3">

                                    <h6 class="text-uppercase text-info text-xs font-weight-bolder mb-3">Dates</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-sm">Created:</span>
                                        <span class="text-sm font-weight-bold">{{ $customer->created_at?->format('M d, Y') ?? '—' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-sm">Updated:</span>
                                        <span class="text-sm font-weight-bold">{{ $customer->updated_at?->format('M d, Y') ?? '—' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-body p-3">
                                    <h6 class="text-uppercase text-info text-xs font-weight-bolder mb-3">Documents</h6>
                                    <ul class="list-group">
                                        @if($crDoc && $crDoc->getFirstMediaUrl('documents'))
                                            <li class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-sm bg-secondary-lt me-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><path d="M9 9l1 0"/><path d="M9 13l6 0"/><path d="M9 17l6 0"/></svg>
                                                    </span>
                                                    <div class="d-flex flex-column">
                                                        <h6 class="mb-1 text-dark text-sm">Company CR</h6>
                                                        <span class="text-xs text-muted">Active</span>
                                                    </div>
                                                </div>
                                                <a class="btn btn-sm btn-outline-primary ms-auto" href="{{ $crDoc->getFirstMediaUrl('documents') }}" target="_blank" rel="noopener">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6s-6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6s6.6 2 9 6"/></svg>
                                                    View
                                                </a>
                                            </li>
                                        @endif

                                        @if($trnDoc && $trnDoc->getFirstMediaUrl('documents'))
                                            <li class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-sm bg-secondary-lt me-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 4h6a2 2 0 0 1 2 2v14l-2 -1l-2 1l-2 -1l-2 1l-2 -1l-2 1v-14a2 2 0 0 1 2 -2"/><path d="M9 8l6 0"/><path d="M9 12l6 0"/><path d="M9 16l6 0"/></svg>
                                                    </span>
                                                    <div class="d-flex flex-column">
                                                        <h6 class="mb-1 text-dark text-sm">Company TRN</h6>
                                                        <span class="text-xs text-muted">Active</span>
                                                    </div>
                                                </div>
                                                <a class="btn btn-sm btn-outline-primary ms-auto" href="{{ $trnDoc->getFirstMediaUrl('documents') }}" target="_blank" rel="noopener">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6s-6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6s6.6 2 9 6"/></svg>
                                                    View
                                                </a>
                                            </li>
                                        @endif

                                        @if (!($crDoc && $crDoc->getFirstMediaUrl('documents')) && !($trnDoc && $trnDoc->getFirstMediaUrl('documents')))
                                            <li class="list-group-item border-0 ps-0 text-secondary">No documents available.</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(false)
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header p-3 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="w-50">
                            <h6> {{ $customer->name }}</h6>
                            <div class="text-secondary">
                                <div class="text-sm">{{ $customer->email }}</div>
                                <div class="text-sm">{{ $customer->phone }}</div>
                            </div>
                        </div>
                        <div class="text-sm mb-1">
                            <span class="badge {{ $customer->status == 'active' ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ ucfirst($customer->status) }}</span>
                        </div>
                        <button type="button" class="btn btn-outline-primary" wire:click="resetStatus" data-bs-toggle="modal" data-bs-target="#update_status_modal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                            Update Status
                        </button>
                    </div>
                </div>
                <div class="card-body p-3 pt-0">
                    <hr class="horizontal dark mt-0 mb-4">
                    <div class="row">
                        <div class="col-md-6 d-flex flex-column">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Profile Information</h6>
                            <ul class="list-group">
                                <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                    <strong class="text-dark">Company:</strong> &nbsp; {{ $customer->company }}
                                </li>
                                <li class="list-group-item border-0 ps-0 text-sm">
                                    <strong class="text-dark">Industry:</strong> &nbsp; {{ $customer->industry }}
                                </li>
                                <li class="list-group-item border-0 ps-0 text-sm">
                                    <strong class="text-dark">Website:</strong> &nbsp;
                                    @if($customer->website)
                                        <a href="{{ $customer->website }}" target="_blank" class="text-primary">{{ $customer->website }}</a>
                                    @else
                                        -
                                    @endif
                                </li>
                                <li class="list-group-item border-0 ps-0 text-sm">
                                    <strong class="text-dark">CRN:</strong> &nbsp; {{ $customer->crn }}
                                </li>
                                <li class="list-group-item border-0 ps-0 text-sm">
                                    <strong class="text-dark">TRN:</strong> &nbsp; {{ $customer->trn }}
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6 ms-auto">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Documents</h6>
                            @php
                                $crDoc = $customer->generalDocuments->where('type', 'cr')->where('status', 'active')->sortByDesc('created_at')->first();
                                $trnDoc = $customer->generalDocuments->where('slug', 'trn')->where('status', 'active')->sortByDesc('created_at')->first();
                            @endphp

                            <ul class="list-group">
                                @if($crDoc && $crDoc->getFirstMediaUrl('documents'))
                                    <li class="list-group-item border-0 d-flex align-items-center px-0 mb-2 pt-0">
                                        <div class="d-flex align-items-center justify-content-center bg-secondary-lt rounded me-3" style="width: 40px; height: 40px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><path d="M9 9l1 0"/><path d="M9 13l6 0"/><path d="M9 17l6 0"/></svg>
                                        </div>
                                        <div class="d-flex align-items-start flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">Company CR</h6>
                                        </div>
                                        <a class="btn btn-link pe-3 ps-0 mb-0 ms-auto" href="{{ $crDoc->getFirstMediaUrl('documents') }}" target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6s-6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6s6.6 2 9 6"/></svg>
                                            View
                                        </a>
                                    </li>
                                @endif

                                @if($trnDoc && $trnDoc->getFirstMediaUrl('documents'))
                                    <li class="list-group-item border-0 d-flex align-items-center px-0 mb-2">
                                        <div class="d-flex align-items-center justify-content-center bg-secondary-lt rounded me-3" style="width: 40px; height: 40px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 4h6a2 2 0 0 1 2 2v14l-2 -1l-2 1l-2 -1l-2 1l-2 -1l-2 1v-14a2 2 0 0 1 2 -2"/><path d="M9 8l6 0"/><path d="M9 12l6 0"/><path d="M9 16l6 0"/></svg>
                                        </div>
                                        <div class="d-flex align-items-start flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">Company TRN</h6>
                                        </div>
                                        <a class="btn btn-link pe-3 ps-0 mb-0 ms-auto" href="{{ $trnDoc->getFirstMediaUrl('documents') }}" target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6s-6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6s6.6 2 9 6"/></svg>
                                            View
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <div class="row">
                    <div class="col-12 col-xl-12 mt-xl-0 mt-4">
                        <div class="card card-plain">
                            <div class="card-header pb-0 p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="mb-0">Address(s)</h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="button" class="btn btn-primary" wire:click="openModal">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                                            Add New Address
                                        </button>
                                    </div>
                                </div>

                            </div>
                            <div class="card-body p-3">
                                <div class="card">
                                    <div class="">
                                        <table class="table table-vcenter card-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Type</th>
                                                    <th>Country</th>
                                                    <th>State</th>
                                                    <th>City</th>
                                                    <th>Address</th>
                                                    <th>Zip</th>
                                                    <th class="w-1">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($customer->addresses as $key => $address)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ ucwords(str_replace('_', ' ', $address->address_type)) }}
                                                        </td>
                                                        <td>{{ $address->country }}</td>
                                                        <td>{{ $address->state }}</td>
                                                        <td>{{ $address->city }}</td>
                                                        <td>{{ $address->line1 }} {{ $address->line2 }}</td>
                                                        <td>{{ $address->postal_code }}</td>
                                                        <td class="text-end">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <button type="button" class="dropdown-item" wire:click="editAddress({{ $address->id }})">Edit</button>
                                                                    <button type="button" class="dropdown-item text-danger" wire:click="deleteAddress({{ $address->id }})">Delete</button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            @endif

    <div class="modal modal-blur fade @if ($showModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if ($showModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Address</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="storeAddress">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="address_type" class="form-label">Type of Address</label>
                                <select id="address_type" wire:model="address_type" class="form-select">
                                    <option value="">Select One</option>
                                    <option value="billing_address">Billing Address</option>
                                    <option value="shipping_address">Shipping Address</option>
                                </select>
                                @error('address_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="country" class="form-label">Country</label>
                                <select id="country" wire:model.live="country" class="form-select">
                                    <option value="">Select One</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->name }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                @error('country')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="state" class="form-label">State</label>
                                <select id="state" wire:model.live="state" class="form-select">
                                    <option value="">Select One</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->name }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                @error('state')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label">City</label>
                                <select id="city" wire:model="city" class="form-select">
                                    <option value="">Select One</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->name }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                @error('city')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="line1" class="form-label">Address Line 1</label>
                                <input type="text" class="form-control" id="line1" wire:model="line1">
                                @error('line1')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="line2" class="form-label">Address Line 2</label>
                                <input type="text" class="form-control" id="line2" wire:model="line2">
                                @error('line2')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="postal_code" wire:model="postal_code">
                                @error('postal_code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeModal">Close</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="storeAddress">
                            <span wire:loading wire:target="storeAddress" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade @if ($showAddressEditModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if ($showAddressEditModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Address</h5>
                    <button type="button" class="btn-close" wire:click="closeAddressEditModal"></button>
                </div>
                <form wire:submit.prevent="updateAddress">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_address_type" class="form-label">Type of Address</label>
                                <select id="edit_address_type" wire:model="address_type" class="form-select">
                                    <option value="">Select One</option>
                                    <option value="billing_address">Billing Address</option>
                                    <option value="shipping_address">Shipping Address</option>
                                </select>
                                @error('address_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="edit_country" class="form-label">Country</label>
                                <select id="edit_country" wire:model.live="country" class="form-select">
                                    <option value="">Select One</option>
                                    @foreach ($countries as $country_item)
                                        <option value="{{ $country_item->name }}">{{ $country_item->name }}</option>
                                    @endforeach
                                </select>
                                @error('country')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6" wire:key="state-select-{{ $addressEditId }}-{{ $country }}">
                                <label for="edit_state" class="form-label">State</label>
                                <select id="edit_state" wire:model.live="state" class="form-select">
                                    <option value="">Select One</option>
                                    @foreach ($states as $state_item)
                                        <option value="{{ $state_item->name }}">{{ $state_item->name }}</option>
                                    @endforeach
                                </select>
                                @error('state')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6" wire:key="city-select-{{ $addressEditId }}-{{ $state }}">
                                <label for="edit_city" class="form-label">City</label>
                                <select id="edit_city" wire:model="city" class="form-select">
                                    <option value="">Select One</option>
                                    @foreach ($cities as $city_item)
                                        <option value="{{ $city_item->name }}">{{ $city_item->name }}</option>
                                    @endforeach
                                </select>
                                @error('city')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="edit_line1" class="form-label">Address Line 1</label>
                                <input type="text" class="form-control" id="edit_line1" wire:model="line1">
                                @error('line1')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="edit_line2" class="form-label">Address Line 2</label>
                                <input type="text" class="form-control" id="edit_line2" wire:model="line2">
                                @error('line2')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="edit_postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="edit_postal_code" wire:model="postal_code">
                                @error('postal_code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeAddressEditModal">Close</button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="updateAddress">
                        <span wire:loading wire:target="updateAddress" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Update
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="update_status_modal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateStatus">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" wire:model="status">
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        @error('status')<div class="invalid-feedback d-block mb-3">{{ $message }}</div>@enderror

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="updateStatus">
                                <span wire:loading wire:target="updateStatus" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Save changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('close-modal', (event) => {
            const modalEl = document.getElementById('update_status_modal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
            }
        });
    });
</script>
@endpush
