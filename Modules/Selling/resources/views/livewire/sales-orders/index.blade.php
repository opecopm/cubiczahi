<div>
    @if(!$isEmbedded)
        @component('admin.partials.page.inner-header', [
            'title' => 'Sales Order Management',
            'breadcrumbs' => [
                [
                    'label' => 'Sales Orders',
                    'active' => true,
                ],
            ],
            'actionItems' => [
                [
                    'title' => 'Add New Sales Order',
                    'route' => 'admin.selling.sales-orders.create',
                    'icon' => 'ti ti-plus',
                    'class' => 'btn btn-primary',
                ],
            ],
        ])
        @endcomponent
    @endif

    <div class="{{ $isEmbedded ? '' : 'page-body' }}">
        <div class="{{ $isEmbedded ? '' : 'container-xl' }}">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="row g-2 w-100">
                        <div class="col">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Search orders"
                                wire:model.live.debounce.300ms="search"
                            >
                        </div>
                        @foreach($model->filterable as $field => $meta)
                            @if($isEmbedded && $field === 'customer_id') @continue @endif
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
                                @else
                                    <input type="text" class="form-control" wire:model.live="filters.{{ $field }}" placeholder="Search {{ ucfirst(str_replace('_',' ',$field)) }}">
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
                                <th>Customer @include('components.table.sort', ['field' => 'customer_id'])</th>
                                <th>Order Date @include('components.table.sort', ['field' => 'order_date'])</th>
                                <th>Total @include('components.table.sort', ['field' => 'total'])</th>
                                <th>Status @include('components.table.sort', ['field' => 'status'])</th>
                                <th class="w-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($salesOrders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>
                                        <a href="{{ $isEmbedded ? route('admin.crm.customers.orders.show', ['customer' => $customerId, 'order' => $order->id]) : route('admin.selling.sales-orders.show', $order->id) }}">
                                            {{ $order->reference }}
                                        </a>
                                    </td>
                                    <td>{{ $order->customer->company ?? '' }} - {{ $order->customer->name ?? '' }}</td>
                                    <td>{{ $order->order_date }}</td>
                                    <td>{{ $order->total }}</td>
                                    <td><span class="badge {{ $order->getStatusBadgeClass() }}">{{ $order->getStatusLabel() }}</span></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ $isEmbedded ? route('admin.crm.customers.orders.show', ['customer' => $customerId, 'order' => $order->id]) : route('admin.selling.sales-orders.show', $order->id) }}" class="dropdown-item">View</a>
                                                <a href="{{ route('admin.selling.sales-orders.edit', $order->id) }}" class="dropdown-item">Edit</a>
                                                <button
                                                    type="button"
                                                    class="dropdown-item text-danger"
                                                    wire:click="confirmDelete({{ $order->id }})"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex align-items-center">
                    {{ $salesOrders->links() }}
                </div>
            </div>
        </div>
    </div>

    @include('admin.livewire.partials.delete-confirmation-modal')
</div>
