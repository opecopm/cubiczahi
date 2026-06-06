<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Edit Sales Order',
        'breadcrumbs' => [
            [
                'label' => 'Sales Orders',
                'url' => route('admin.selling.sales-orders.index'),
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
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
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
                                placeholder="{{ Modules\Global\Models\ReferenceSchema::getNextReference('sales_order') ?? '' }}"
                                readonly
                            >
                            @error('reference')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select id="customer_id" class="form-select" wire:model="customer_id">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" value="{{ $statuses[$status] ?? ucfirst($status) }}" readonly>
                            <small class="text-muted">Status can be changed from the view page.</small>
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
                    <button type="button" class="btn btn-primary" wire:click="update" wire:loading.class="btn-loading" wire:target="update">Update Sales Order</button>
                </div>
            </div>
        </div>
    </div>

    @include('selling::livewire.sales-orders.partials.add-item-modal')

    @push('js')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('quillEditor', () => ({
                    quill: null,
                    init(initialHtml) {
                        if (this.quill) return;
                        this.quill = new Quill(this.$el.querySelector('#description'), { theme: 'snow' });
                        this.quill.root.innerHTML = initialHtml || '';
                        this.quill.on('text-change', () => {
                            @this.set('description', this.quill.root.innerHTML);
                        });
                        window.addEventListener('init-description-quill', e => {
                            this.quill.root.innerHTML = e.detail.html || '';
                        });
                    }
                }));
            });

            document.addEventListener('livewire:init', () => {
                Livewire.on('init-description-quill', ({ html }) => {
                    window.dispatchEvent(new CustomEvent('init-description-quill', { detail: { html } }));
                });
            });
        </script>
    @endpush
</div>
