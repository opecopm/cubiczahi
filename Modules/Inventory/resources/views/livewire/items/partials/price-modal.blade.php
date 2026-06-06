<div class="modal modal-blur fade @if($priceModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($priceModal) aria-modal="true" @else aria-hidden="true" @endif>
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <form wire:submit.prevent="savePrice" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    {{ isset($priceData['id']) ? 'Edit Item Price' : 'Add Item Price' }}
                </h5>
                <button type="button" class="btn-close" wire:click="closePriceModal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">

                    <!-- Price Type -->
                    <div class="col-md-6">
                        <label class="form-label">Price Type</label>
                        <select class="form-select" wire:model.live="priceData.price_type">
                            <option value="">Select Type</option>
                            <option value="selling">Selling</option>
                            <option value="purchase">Purchase</option>
                        </select>
                        @error('priceData.price_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <!-- Price -->
                    <div class="col-md-6">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" wire:model.defer="priceData.price">
                        @error('priceData.price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <!-- Customer (if selling) -->
                    @if(@$priceData['price_type'] === 'selling')
                        <div class="col-md-6 position-relative">
                            <label class="form-label">Customer (optional)</label>
                            <input type="text" class="form-control" wire:model.live.debounce.500ms="customerSearch" placeholder="Search Customer...">

                            @if(!empty($customerResults))
                                <ul class="list-group position-absolute w-100 shadow mt-1" style="z-index: 1000;">
                                    @foreach($customerResults as $customer)
                                        <li class="list-group-item list-group-item-action cursor-pointer"
                                            wire:click="selectCustomer({{ $customer->id }}, '{{ addslashes($customer->name) }}')">
                                            {{ $customer->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            @if(@$priceData['customer_id'])
                                <small class="text-muted d-block mt-1">
                                    <a href="#" class="text-danger ms-2" wire:click.prevent="clearCustomer">Clear</a>
                                </small>
                            @endif
                        </div>
                    @endif

                    <!-- Currency -->
                    <div class="col-md-6">
                        <label class="form-label">Currency</label>
                        <select class="form-select" wire:model.live="priceData.currency_id">
                            <option value="">Select Currency</option>
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->id }}">{{ $curr->name }} ({{ $curr->code }})</option>
                            @endforeach
                        </select>
                        @error('priceData.currency_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <!-- Currency Rate (Read-Only) -->
                    <div class="col-md-6">
                        <label class="form-label">Currency Rate</label>
                        <input type="number" step="0.0001" class="form-control" wire:model="priceData.currency_rate" readonly>
                    </div>


                    <!-- Date From -->
                    <div class="col-md-6">
                        <label class="form-label">Valid From</label>
                        <input type="text" class="form-control datepicker" wire:model.defer="priceData.date_from">
                        @error('priceData.date_from')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <!-- Date To -->
                    <div class="col-md-6">
                        <label class="form-label">Valid To</label>
                        <input type="text" class="form-control datepicker" wire:model.defer="priceData.date_to">
                        @error('priceData.date_to')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <!-- Default Checkbox -->
                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_default" wire:model="priceData.is_default">
                            <label class="form-check-label" for="is_default">Set as Default</label>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closePriceModal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    {{ isset($priceData['id']) ? 'Update' : 'Save' }}
                </button>
            </div>

        </form>
    </div>
</div>
