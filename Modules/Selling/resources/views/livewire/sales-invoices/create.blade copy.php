<div class="container-fluid py-4">
    <style>
        label, select {
            width: 100%;
        }
    </style>
    <div class="row">
        <div class="col-10 m-auto">
            <h3 class="mt-3 mb-0 text-center">Create Sales Invoice</h3>
            <p class="lead font-weight-normal opacity-8 mb-3 text-center">Fill in the details for the new sales invoice.</p>
            <div class="card">
                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif
                    <div class="sales-invoice-form">
                        <!-- Basic Invoice Info -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-outline">
                                    <label for="reference">Reference</label>
                                    <input type="text" id="reference" class="form-control" wire:model="reference" placeholder="{{Modules\Global\Models\ReferenceSchema::getNextReference('sales_invoice')??''}}" readonly>
                                </div>
                                @error('reference')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-md-6 mb-3" style="position: relative;">
                                <div class="input-group input-group-outline">
                                    <label for="customer_reference">Customer (<a href="{{url('crm/customers/create')}}" target="_blank">Add New</a>)</label>
                                    <input type="text" class="form-control" wire:model.live="customer_reference" placeholder="Search Customer Reference...">
                                    <input type="hidden" wire:model="customer_id">
                                </div>
                                @error('customer_reference')<span class="text-danger">{{ $message }}</span>@enderror
                                <div class="suggestions-list {{$customerSuggestionsList != 'show'?'d-none':''}}">
                                    <ul class="list-group mt-2">
                                        @forelse($customerSuggestions as $suggestion)
                                            <li class="list-group-item suggestion-item">
                                                <a href="java-script:voide(0)" wire:click="selectCustomer('{{$suggestion->reference}}')">{{ $suggestion->reference }} - {{ $suggestion->name }}</a>
                                            </li>
                                        @empty
                                        <li class="list-group-item suggestion-item">
                                           No Data Found!
                                        </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>

                            <!-- Invoice Date, Status -->
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-outline">
                                    <label for="invoice_date">Invoice Date</label>
                                    <input type="date" id="invoice_date" class="form-control" wire:model="invoice_date">
                                </div>
                                @error('invoice_date')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-outline">
                                    <label for="due_date">Due Date</label>
                                    <input type="date" id="due_date" class="form-control" wire:model="due_date">
                                </div>
                                @error('due_date')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-md-6 mb-3" style="position: relative;">
                                <div class="input-group input-group-outline">
                                    <label for="title">Sales Order</label>
                                    <input type="text" class="form-control" id="sales_order_reference" wire:model.live="sales_order_reference" placeholder="Search Sales Order Reference">
                                </div>
                                @error('sales_order_reference')<span class="text-danger">{{ $message }}</span>@enderror
                                <div class="suggestions-list {{$salesOrderSuggestionsList != 'show'?'d-none':''}}">
                                    <ul class="list-group mt-2">
                                        @forelse($salesOrderSuggestions as $suggestion)
                                            <li class="list-group-item suggestion-item">
                                                <a href="java-script:voide(0)" wire:click="selectSalesOrder('{{$suggestion->reference}}')">{{ $suggestion->reference }}</a>
                                            </li>
                                        @empty
                                        <li class="list-group-item suggestion-item">
                                           No Data Found!
                                        </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-outline">
                                    <label for="status">Status</label>
                                    <select id="status" class="form-control" wire:model="status">
                                        @foreach ($statuses as $key=>$status)
                                            <option value="{{$key}}">{{$status}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('status')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <!-- Invoice Items -->
                        <h4 class="mt-4">Invoice Items</h4>
                        <div class="table-responsive">
                            <!-- Display Added Items -->
                            <table class="table mt-4">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Discount</th>
                                        <th>Subtotal</th>
                                        <th>Tax</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($items) > 0)
                                    @foreach($items as $index => $item)
                                    <tr>
                                        <!-- Item -->
                                        <td>
                                            <b>{{ $item['name'] }}</b><br>
                                            {!! $item['description'] !!}
                                        </td>

                                        <!-- Quantity -->
                                        <td>{{ $item['quantity'] }}</td>

                                        <!-- Selling Price -->
                                        <td>{{ number_format($item['price'], 2) }}</td>

                                        <!-- Selling Discount -->
                                        <td>
                                            @if($item['discount_type'] === 'percent')
                                            {{ number_format($item['discount'], 2) }}<br>
                                            <span class="text-xs">{{ number_format($item['discount_rate'], 2) }}%</span>
                                        @else
                                            {{ number_format($item['discount'], 2) }} (Fixed)
                                        @endif
                                        </td>

                                        <!-- Selling Subtotal -->
                                        <td>
                                            {{ number_format($item['subtotal'], 2) }}
                                        </td>

                                        <!-- Selling Tax -->
                                        <td>
                                            {{ number_format($item['tax_amount'], 2) }}<br>
                                            <small class="text-xs">{{ $item['tax_rate'] ?? '' }}% {{ $item['tax_name'] ?? '' }}</small>
                                        </td>

                                        <!-- Selling Total -->
                                        <td>{{ number_format($item['total'], 2) }}</td>



                                        <!-- Actions -->
                                        <td>
                                            <button class="btn btn-primary btn-sm" wire:click="editItem({{ $index }})"><i class="fa fa-pencil"></i></button>
                                            <button type="button" class="btn btn-danger btn-sm" wire:click="removeItem({{ $index }})"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>


                        <div class="row">
                            <div class="col-md-3">
                                <div class="input">
                                    <button type="button" class="btn btn-primary" wire:click="openAddItemModal">Add Item</button>
                                </div>
                            </div>
                        </div>

                        <!-- Totals -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="input-group input-group-outline">
                                    <label for="subtotal">Subtotal</label>
                                    <input type="text" id="subtotal" class="form-control" wire:model="subtotal" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-outline">
                                    <label for="total">Tax Amount</label>
                                    <input type="text" id="total" class="form-control" wire:model="tax" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-outline">
                                    <label for="total">Total</label>
                                    <input type="text" id="total" class="form-control" wire:model="total" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline">
                                    <label for="paid_amount">Paid Amount</label>
                                    <input type="number" id="paid_amount" class="form-control" wire:model="paid_amount">
                                </div>
                                @error('paid_amount')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-md-6">
                                <div class="input-group input-group-outline">
                                    <label for="due_amount">Due Amount</label>
                                    <input type="text" id="due_amount" class="form-control" wire:model="due_amount" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary btn-loading" wire:click="store">
                        Create Sales Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include('selling::livewire.sales-invoices.partials.add-item-modal')
    @push('js')
        <script>
            document.addEventListener('alpine:init', () => {
                setTimeout(() => {
                    const contentElement = document.getElementById('description');
                    if (contentElement) {
                        const quill = new Quill('#description', { theme: 'snow' });

                        quill.root.innerHTML = @js($page['description'] ?? '');

                        quill.on('text-change', function () {
                            @this.set('description', quill.root.innerHTML);
                        });
                    }
                }, 300);
            })
        </script>
    @endpush
</div>
