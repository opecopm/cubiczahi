<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Edit Sales Invoice',
        'breadcrumbs' => [
            [
                'label' => 'Sales Invoices',
                'url' => route('admin.selling.sales-invoices.index'),
                'icon' => 'back',
            ],
            [
                'label' => 'Edit',
                'active' => true,
            ],
        ],
        'actionItems' => [
            [
                'title' => 'Back',
                'route' => 'admin.selling.sales-invoices.index',
                'icon' => 'ti ti-arrow-left',
                'class' => 'btn btn-outline-secondary',
            ],
        ],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="reference" class="form-label">Reference</label>
                            <input type="text" id="reference" class="form-control" wire:model="reference" readonly>
                            @error('reference')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 position-relative">
                            <label for="customer_reference" class="form-label">
                                Customer (<a href="{{ url('crm/customers/create') }}" target="_blank">Add New</a>)
                            </label>
                            <input
                                type="text"
                                id="customer_reference"
                                class="form-control"
                                wire:model.live="customer_reference"
                                placeholder="Search Customer Reference..."
                            >
                            <input type="hidden" wire:model="customer_id">
                            @error('customer_reference')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <div class="suggestions-list {{ $customerSuggestionsList != 'show' ? 'd-none' : '' }}">
                                <ul class="list-group mt-2">
                                    @forelse($customerSuggestions as $suggestion)
                                        <li class="list-group-item suggestion-item">
                                            <a href="javascript:void(0)" wire:click="selectCustomer('{{ $suggestion->reference }}')">
                                                {{ $suggestion->reference }} - {{ $suggestion->name }}
                                            </a>
                                        </li>
                                    @empty
                                        <li class="list-group-item suggestion-item">No Data Found!</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="invoice_date" class="form-label">Invoice Date</label>
                            <input
                                type="text"
                                id="invoice_date"
                                class="form-control datepicker"
                                placeholder="yyyy-mm-dd"
                                wire:model="invoice_date"
                            >
                            @error('invoice_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input
                                type="text"
                                id="due_date"
                                class="form-control datepicker"
                                placeholder="yyyy-mm-dd"
                                wire:model="due_date"
                            >
                            @error('due_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 position-relative">
                            <label for="sales_order_reference" class="form-label">Sales Order</label>
                            <input
                                type="text"
                                class="form-control"
                                id="sales_order_reference"
                                wire:model.live="sales_order_reference"
                                placeholder="Search Sales Order Reference"
                            >
                            @error('sales_order_reference')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <div class="suggestions-list {{ $salesOrderSuggestionsList != 'show' ? 'd-none' : '' }}">
                                <ul class="list-group mt-2">
                                    @forelse($salesOrderSuggestions as $suggestion)
                                        <li class="list-group-item suggestion-item">
                                            <a href="javascript:void(0)" wire:click="selectSalesOrder('{{ $suggestion->reference }}')">
                                                {{ $suggestion->reference }}
                                            </a>
                                        </li>
                                    @empty
                                        <li class="list-group-item suggestion-item">No Data Found!</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" class="form-select" wire:model="status">
                                @foreach ($statuses as $key => $status)
                                    <option value="{{ $key }}">{{ $status }}</option>
                                @endforeach
                            </select>
                            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="hr my-4"></div>

                    <h3 class="card-title mb-3">Invoice Items</h3>

                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Discount</th>
                                    <th>Subtotal</th>
                                    <th>Tax</th>
                                    <th>Total</th>
                                    <th class="w-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $index => $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item['name'] }}</strong><br>
                                            {!! $item['description'] !!}
                                        </td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>{{ number_format($item['price'], 2) }}</td>
                                        <td>
                                            @if ($item['discount_type'] === 'percent')
                                                {{ number_format($item['discount'], 2) }}<br>
                                                <span class="text-secondary">{{ number_format($item['discount_rate'], 2) }}%</span>
                                            @else
                                                {{ number_format($item['discount'], 2) }} (Fixed)
                                            @endif
                                        </td>
                                        <td>{{ number_format($item['subtotal'], 2) }}</td>
                                        <td>
                                            {{ number_format($item['tax_amount'], 2) }}<br>
                                            <small class="text-secondary">
                                                {{ $item['tax_rate'] ?? '' }}% {{ $item['tax_name'] ?? '' }}
                                            </small>
                                        </td>
                                        <td>{{ number_format($item['total'], 2) }}</td>
                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="editItem({{ $index }})">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeItem({{ $index }})">
                                                    Remove
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" wire:click="openAddItemModal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Add Item
                        </button>
                    </div>

                    <div class="hr my-4"></div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="subtotal" class="form-label">Subtotal</label>
                            <input type="text" id="subtotal" class="form-control" wire:model="subtotal" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="tax" class="form-label">Tax Amount</label>
                            <input type="text" id="tax" class="form-control" wire:model="tax" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="total" class="form-label">Total</label>
                            <input type="text" id="total" class="form-control" wire:model="total" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="paid_amount" class="form-label">Paid Amount</label>
                            <input type="number" id="paid_amount" class="form-control" wire:model="paid_amount">
                            @error('paid_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="due_amount" class="form-label">Due Amount</label>
                            <input type="text" id="due_amount" class="form-control" wire:model="due_amount" readonly>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button type="button" class="btn btn-primary" wire:click="update" wire:loading.class="btn-loading" wire:target="update">Update Sales Invoice</button>
                </div>
            </div>
        </div>
    </div>

    @include('selling::livewire.sales-invoices.partials.add-item-modal')
</div>

@push('js')
<script>
    document.addEventListener("livewire:navigated", initQuill);
    document.addEventListener("livewire:load", initQuill);

    function initQuill() {
        if (document.getElementById('description')) {
            if (document.getElementById('description').classList.contains('ql-container')) {
                return;
            }

            var quill = new Quill('#description', { theme: 'snow' });
            quill.on('text-change', function () {
                let value = document.querySelector('#description .ql-editor').innerHTML;
                @this.set('description', value);
            });
        }
    }
</script>
@endpush
