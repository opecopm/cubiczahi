<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Create Sales Order',
        'breadcrumbs' => [
            [
                'label' => 'Sales Orders',
                'url' => route('admin.selling.sales-orders.index'),
                'icon' => 'back',
            ],
            [
                'label' => 'Create',
                'active' => true,
            ],
        ],
        'actionItems' => [
            [
                'title' => 'Back',
                'route' => 'admin.selling.sales-orders.index',
                'icon' => 'ti ti-arrow-left',
                'class' => 'btn btn-outline-secondary',
            ],
        ],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            @if (session()->has('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="reference" class="form-label">Reference</label>
                            <input
                                type="text"
                                id="reference"
                                class="form-control"
                                wire:model="reference"
                                placeholder="{{ \Modules\Global\Models\ReferenceSchema::getNextReference('sales_order') }}"
                                readonly
                            >
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
                                                {{ $suggestion->reference }} – {{ $suggestion->name }}
                                            </a>
                                        </li>
                                    @empty
                                        <li class="list-group-item suggestion-item">No Data Found!</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="order_date" class="form-label">Order Date</label>
                            <input type="date" id="order_date" class="form-control" wire:model="order_date">
                            @error('order_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="delivery_date" class="form-label">Due Date</label>
                            <input type="date" id="delivery_date" class="form-control" wire:model="delivery_date">
                            @error('delivery_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" class="form-select" wire:model="status">
                                @foreach(\Modules\Selling\Models\SalesOrder::STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="hr my-4"></div>

                    <h3 class="card-title mb-3">Order Items</h3>
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
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td><strong>{{ $item['name'] }}</strong><br>{!! $item['description'] !!}</td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>{{ number_format($item['price'],2) }}</td>
                                        <td>
                                            @if($item['discount_type']=='percent')
                                                {{ number_format($item['discount'],2) }}<br>
                                                <small class="text-secondary">{{ number_format($item['discount_rate'],2) }}%</small>
                                            @else
                                                {{ number_format($item['discount'],2) }} (Fixed)
                                            @endif
                                        </td>
                                        <td>{{ number_format($item['subtotal'],2) }}</td>
                                        <td>
                                            {{ number_format($item['tax_amount'],2) }}<br>
                                            <small class="text-secondary">{{ $item['tax_rate'] ?? '' }}% {{ $item['tax_name'] ?? '' }}</small>
                                        </td>
                                        <td>{{ number_format($item['total'],2) }}</td>
                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="editItem({{ $index }})">Edit</button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeItem({{ $index }})">Remove</button>
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
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button type="button" class="btn btn-primary" wire:click="store" wire:loading.class="btn-loading" wire:target="store">Create Sales Order</button>
                </div>
            </div>
        </div>
    </div>
    @include('selling::livewire.sales-orders.partials.add-item-modal')
</div>

@push('js')
<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('init-description-quill', html => {
        window.dispatchEvent(new CustomEvent('init-description-quill', { detail: { html } }));
    });
});

</script>
@endpush
