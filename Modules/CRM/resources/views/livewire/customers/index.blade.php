<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Customer Management',
        'breadcrumbs' => [
            [
                'label' => 'Customers',
                'active' => true,
            ],
        ],
        'actionItems' => [
            [
                'title' => 'Add New Customer',
                'route' => 'admin.crm.customers.create',
                'icon' => 'ti ti-plus',
                'class' => 'btn btn-sm btn-primary',
            ],
        ],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            {{-- Stats --}}
            <div class="row row-deck row-cards mb-3">
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.status', '')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="bg-blue text-white avatar">{{ $customersCount }}</span></div>
                                <div class="col"><div class="text-secondary">All Customers</div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.status', 'active')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="bg-green text-white avatar">{{ $activeCustomersCount }}</span></div>
                                <div class="col"><div class="text-secondary">Active Customers</div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.status', 'inactive')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="bg-secondary text-white avatar">{{ $inactiveCustomersCount }}</span></div>
                                <div class="col"><div class="text-secondary">Inactive Customers</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="row g-2 w-100">
                        <div class="col">
                            <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search by name or email">
                        </div>
                        @foreach($model->filterable as $field => $meta)
                            <div class="col">
                                @if(($meta['type'] ?? null) === 'select')
                                    <select class="form-select" wire:model.live="filters.{{ $field }}">
                                        <option value="">{{ ucfirst(str_replace('_',' ',$field)) }}: All</option>
                                        @foreach(($meta['options'] ?? []) as $key => $val)
                                            <option value="{{ $key }}">{{ is_array($val) ? ($val['name'] ?? reset($val)) : $val }}</option>
                                        @endforeach
                                    </select>
                                @elseif(($meta['type'] ?? null) === 'date')
                                    <input type="date" class="form-control" wire:model.live="filters.{{ $field }}">
                                @elseif(($meta['type'] ?? null) === 'text')
                                    <input type="text" class="form-control" placeholder="Search {{ ucfirst($field) }}" wire:model.live="filters.{{ $field }}">
                                @endif
                            </div>
                        @endforeach
                        <div class="col-auto">
                            <button wire:click="resetFilters" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>ID @include('components.table.sort', ['field' => 'id'])</th>
                                <th>Reference @include('components.table.sort', ['field' => 'reference'])</th>
                                <th>Name @include('components.table.sort', ['field' => 'name'])</th>
                                <th>Company</th>
                                <th>Email @include('components.table.sort', ['field' => 'email'])</th>
                                <th>Phone</th>
                                <th>Group</th>
                                <th>Status</th>
                                <th class="w-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $customer->id }}</td>
                                    <td><a href="{{ route('admin.crm.customers.show', ['customer' => $customer->id]) }}">{{ $customer->reference }}</a></td>
                                    <td>{{ $customer->name }}</td>
                                    <td>
                                        <div>
                                            <div>{{ $customer->company }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $customer->email }}</td>
                                    <td>+{{ $customer->phone_code }} {{ $customer->phone }}</td>
                                    <td>{{ optional($customer->customerGroup)->name }}</td>
                                    <td>
                                        <span class="badge {{ $customer->status === 'active' ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                            {{ ucfirst($customer->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ route('admin.crm.customers.show', ['customer' => $customer->id]) }}" class="dropdown-item">View</a>
                                                <a href="{{ route('admin.crm.customers.edit', ['customer' => $customer->id]) }}" class="dropdown-item">Edit</a>
                                                <button type="button" class="dropdown-item text-danger" wire:click="confirmDelete({{ $customer->id }})">Delete</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal modal-blur fade @if($showDeleteModal ?? false) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showDeleteModal ?? false) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-title">Are you sure?</div>
                    <div>Do you really want to delete this customer?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="$set('showDeleteModal', false)">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
