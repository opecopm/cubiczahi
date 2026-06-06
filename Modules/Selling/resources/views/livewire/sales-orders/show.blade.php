<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Sales Order #' . $order->reference,
        'breadcrumbs' => $isEmbedded ? [
            ['label' => 'Orders', 'url' => route('admin.crm.customers.orders', $customerId), 'icon' => 'back'],
            ['label' => $order->reference, 'active' => true],
        ] : [
            ['label' => 'Sales Orders', 'url' => route('admin.selling.sales-orders.index'), 'icon' => 'back'],
            ['label' => $order->reference, 'active' => true],
        ],
        'actionItems' => [
            [
                'type' => 'badge',
                'title' => $order->getStatusLabel(),
                'class' => $order->getStatusBadgeClass(),
            ],
            [
                'title' => 'Print',
                'route' => 'admin.selling.sales-orders.print',
                'params' => ['id' => $order->id],
                'icon' => 'ti ti-printer',
                'class' => 'btn btn-sm btn-outline-primary',
            ],
            [
                'title' => 'Edit',
                'route' => 'admin.selling.sales-orders.edit',
                'params' => ['sales_order' => $order->id],
                'icon' => 'ti ti-edit',
                'class' => 'btn btn-sm btn-success',
            ],
            $order->invoice ? [
                'title' => 'Go to Invoice',
                'route' => $isEmbedded ? 'admin.crm.customers.invoices.show' : 'admin.selling.sales-invoices.show',
                'params' => $isEmbedded ? ['customer' => $customerId, 'invoice' => $order->invoice->id] : $order->invoice->id,
                'icon' => 'ti ti-file-invoice',
                'class' => 'btn btn-sm btn-info',
            ] : [
                'title' => 'Generate Invoice',
                'route' => 'admin.selling.sales-invoices.create',
                'params' => ['sales_order' => $order->id],
                'icon' => 'ti ti-file-invoice',
                'class' => 'btn btn-sm btn-primary',
            ],
        ],
    ])
    @slot('actions')
        @foreach($order->getAllowedNextStatuses() as $nextStatus)
            <button wire:click="confirmUpdateStatus('{{ $nextStatus }}')" class="btn btn-sm btn-outline-secondary">
                Mark as {{ \Modules\Selling\Models\SalesOrder::STATUS_SELECT[$nextStatus] ?? ucfirst($nextStatus) }}
            </button>
        @endforeach
    @endslot
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            <div class="row mt-3">
                <div class="col-12 col-lg-8">
                    {{-- Order Information Card --}}
                    <div class="card mb-4">
                        <div class="card-header p-3">
                            <h5 class="mb-0">Order Information</h5>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Reference</span>
                                    <span class="text-sm font-weight-bold">{{ $order->reference }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Status</span>
                                    <span class="badge {{ $order->getStatusBadgeClass() }}">{{ $order->getStatusLabel() }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Customer</span>
                                    <span class="text-sm font-weight-bold">{{ $order->customer->company ?? $order->customer->name ?? '—' }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Order Date</span>
                                    <span class="text-sm font-weight-bold">{{ $order->order_date }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Delivery Date</span>
                                    <span class="text-sm font-weight-bold">{{ $order->delivery_date ?? '—' }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="text-xs text-uppercase text-secondary font-weight-bolder d-block">Total</span>
                                    <span class="text-sm font-weight-bold">{{ number_format($order->total, 2) }} {{ $order->currency }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Items Table --}}
                    <div class="card mb-4">
                        <div class="card-header p-3">
                            <h5 class="mb-0">Items</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Discount</th>
                                        <th>Subtotal</th>
                                        <th>Tax</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <span class="font-weight-bold d-block text-sm">{{ $item->name }}</span>
                                            </td>
                                            <td>{{ $item->quantity }} {{ $item->unit }}</td>
                                            <td>{{ number_format($item->price, 2) }}</td>
                                            <td>
                                                @if($item->discount_type === 'percent')
                                                    {{ $item->discount_rate }}% ({{ number_format($item->discount, 2) }})
                                                @else
                                                    {{ number_format($item->discount, 2) }}
                                                @endif
                                            </td>
                                            <td>{{ number_format($item->subtotal, 2) }}</td>
                                            <td>{{ number_format($item->tax, 2) }}</td>
                                            <td>{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex justify-content-end">
                                <div class="text-end" style="width: 300px;">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-secondary"><strong>Subtotal:</strong></span>
                                        <span class="text-dark">{{ number_format($order->subtotal, 2) }}</span>
                                    </div>
                                    @if($order->discount)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-secondary"><strong>Discount:</strong></span>
                                        <span class="text-dark">{{ number_format($order->discount, 2) }}</span>
                                    </div>
                                    @endif
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-secondary"><strong>Tax:</strong></span>
                                        <span class="text-dark">{{ number_format($order->tax, 2) }}</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="mb-0 text-dark"><strong>Total:</strong></h3>
                                        <h3 class="mb-0 text-dark"><strong>{{ number_format($order->total - ($order->discount ?? 0), 2) }}</strong></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column (Overview) --}}
                <div class="col-12 col-lg-4">
                    <div class="card mb-4">
                        <div class="card-body p-3">
                            <h6 class="text-uppercase text-info text-xs font-weight-bolder mb-3">Overview</h6>
                            <ul class="list-group">
                                <li class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm {{ $order->getStatusBadgeClass() }} me-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/><path d="M12 7v5l3 3"/></svg>
                                        </span>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Status</h6>
                                            <span class="text-xs">{{ $order->getStatusLabel() }}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-info-lt me-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/></svg>
                                        </span>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Customer</h6>
                                            <span class="text-xs">{{ $order->customer->name ?? '—' }}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-primary-lt me-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 3l-4 4l-4 -4"/><path d="M12 7v14"/></svg>
                                        </span>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Subtotal</h6>
                                            <span class="text-xs">{{ number_format($order->subtotal, 2) }} {{ $order->currency }}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-warning-lt me-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l6 -6"/><path d="M12 10l.01 0"/><path d="M12 14l.01 0"/><circle cx="12" cy="12" r="9"/></svg>
                                        </span>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Tax</h6>
                                            <span class="text-xs">{{ number_format($order->tax, 2) }} {{ $order->currency }}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item border-0 d-flex align-items-center ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-success-lt me-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2"/><path d="M12 3v3m0 12v3"/></svg>
                                        </span>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Total</h6>
                                            <span class="text-xs font-weight-bold">{{ number_format($order->total, 2) }} {{ $order->currency }}</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            <hr class="my-3">

                            <h6 class="text-uppercase text-info text-xs font-weight-bolder mb-3">Dates</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-sm">Created:</span>
                                <span class="text-sm font-weight-bold">{{ $order->created_at?->format('M d, Y') ?? '—' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-sm">Updated:</span>
                                <span class="text-sm font-weight-bold">{{ $order->updated_at?->format('M d, Y') ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Update Confirmation Modal --}}
    <div class="modal modal-blur fade @if($showStatusModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showStatusModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-title">Confirm Status Update</div>
                    <div class="mb-3">
                        Are you sure you want to change the status to <strong>{{ \Modules\Selling\Models\SalesOrder::STATUS_SELECT[$nextStatus] ?? ucfirst($nextStatus) }}</strong>?
                    </div>
                    @if($order->customer)
                    <div>
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model.live="notifyCustomer">
                            <span class="form-check-label">Send notification email to customer</span>
                        </label>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="$set('showStatusModal', false)">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="processStatusUpdate">Confirm Update</button>
                </div>
            </div>
        </div>
    </div>
</div>
