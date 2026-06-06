<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Sales Invoice Management',
        'breadcrumbs' => [
            [
                'label' => 'Sales Invoices',
                'active' => true,
            ],
        ],
        'actionItems' => [
            [
                'title' => 'Add New Sales Invoice',
                'route' => 'admin.selling.sales-invoices.create',
                'icon' => 'ti ti-plus',
                'class' => 'btn btn-primary',
            ],
        ],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
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
                                placeholder="Search invoices"
                                wire:model.live.debounce.300ms="search"
                            >
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
                                <th>Issued At @include('components.table.sort', ['field' => 'issued_at'])</th>
                                <th>Total @include('components.table.sort', ['field' => 'total'])</th>
                                <th>Status @include('components.table.sort', ['field' => 'status'])</th>
                                <th>Purchase Total</th>
                                <th>No. of Items</th>
                                <th>Items</th>
                                <th class="w-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($salesInvoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->id }}</td>
                                    <td><a href="{{ $customerId ? route('admin.crm.customers.invoices.show', ['customer' => $customerId, 'invoice' => $invoice->id]) : route('admin.selling.sales-invoices.show', $invoice->id) }}">{{ $invoice->reference }}</a></td>
                                    <td>{{ $invoice->customer->company ?? '' }}</td>
                                    <td>{{ $invoice->invoice_date }}</td>
                                    <td>{{ number_format($invoice->total, 2) }}</td>
                                    <td>{{ $invoice->status }}</td>
                                    <td>
                                        {{ @$invoice->purchaseOrders ? number_format($invoice->purchaseOrders->sum('total'), 2) : '' }}
                                    </td>
                                    <td>
                                        {{ @$invoice->items ? count($invoice->items) : 0 }}
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($invoice->items as $item)
                                                <li>{{ $item->name }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ $customerId ? route('admin.crm.customers.invoices.show', ['customer' => $customerId, 'invoice' => $invoice->id]) : route('admin.selling.sales-invoices.show', $invoice->id) }}" class="dropdown-item">View</a>
                                                <a
                                                    href="{{ route('admin.selling.sales-invoices.print', $invoice->id) }}"
                                                    class="dropdown-item"
                                                    target="_blank"
                                                >
                                                    Print
                                                </a>
                                                @if($invoice->status !== 'final')
                                                    <a href="{{ route('admin.selling.sales-invoices.edit', $invoice->id) }}" class="dropdown-item">Edit</a>
                                                    <button
                                                        type="button"
                                                        class="dropdown-item text-danger"
                                                        wire:click="confirmDelete({{ $invoice->id }})"
                                                    >
                                                        Delete
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex align-items-center">
                    {{ $salesInvoices->links() }}
                </div>
            </div>
        </div>
    </div>

    @include('admin.livewire.partials.delete-confirmation-modal')
</div>
