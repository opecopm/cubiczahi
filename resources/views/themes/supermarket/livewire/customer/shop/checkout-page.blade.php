<div>
    <div class="row g-5">
        {{-- Delivery & Details --}}
        <div class="col-lg-7">
            <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                <h4 class="fw-bold mb-4">1. Delivery Method</h4>
                <div class="row g-3">
                    @foreach($deliveryMethods as $method)
                        <div class="col-md-6">
                            <label class="w-100">
                                <div class="card h-100 {{ $deliveryMethodId == $method->id ? 'border-primary shadow-sm' : '' }}" style="cursor:pointer; {{ $deliveryMethodId == $method->id ? 'background-color:#f0fdf4;' : '' }}">
                                    <div class="card-body">
                                        <div class="form-check d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="radio" wire:model.live="deliveryMethodId" value="{{ $method->id }}">
                                            <h6 class="form-check-label fw-bold text-dark mb-0">
                                                {{ $method->name }}
                                            </h6>
                                        </div>
                                        <div class="text-muted mt-2" style="font-size:0.9rem; padding-left:1.8rem;">
                                            @if($method->price > 0)
                                                +{{ number_format($method->price, 2) }} SAR
                                            @else
                                                Free
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('deliveryMethodId')
                    <span class="text-danger mt-2 d-block" style="font-size:0.85rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                <h4 class="fw-bold mb-4">2. Order Notes (Optional)</h4>
                <textarea class="form-control" rows="4" wire:model="note" placeholder="Any special instructions for delivery..."></textarea>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="col-lg-5">
            <div class="bg-white rounded-4 shadow-sm p-4 sticky-top" style="top:100px;">
                <h4 class="fw-bold mb-4">Order Summary</h4>
                
                <div class="mb-4">
                    @foreach($cartItems as $cartId => $details)
                        @php
                            $item = $itemsData[$details['id']] ?? null;
                            $currencyCode = collect($item['prices'] ?? [])->where('price_type','sell')->first()['currency'] ?? 'SAR';
                            $unitPrice = $details['price'] ?? 0;
                            $itemName = $details['name'] ?? ($item['name']['en'] ?? 'Item');
                            $itemDesc = $details['description'] ?? '';
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                @if($item['primary_image'])
                                    <img src="{{ $item['primary_image']['url'] }}" alt="" class="rounded" style="width:48px; height:48px; object-fit:cover;">
                                @else
                                    <div class="rounded d-flex align-items-center justify-content-center bg-light" style="width:48px; height:48px;">
                                        <i class="fa fa-box text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size:0.95rem;">{{ $itemName }}</h6>
                                    
                                    @if(!empty($itemDesc) && $itemDesc !== $itemName)
                                        <div class="text-muted" style="font-size:0.75rem;">
                                            {{ $itemDesc }}
                                        </div>
                                    @endif
                                    
                                    <span class="text-muted" style="font-size:0.85rem;">Qty: {{ $details['quantity'] }}</span>
                                </div>
                            </div>
                            <div class="fw-bold text-dark">
                                {{ number_format($unitPrice * $details['quantity'], 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between mb-3 text-muted">
                    <span>Subtotal</span>
                    <span>{{ number_format($this->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3 text-muted">
                    <span>Delivery Fees</span>
                    <span>
                        @if($this->deliveryPrice > 0)
                            {{ number_format($this->deliveryPrice, 2) }}
                        @else
                            Free
                        @endif
                    </span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-4 fw-bold fs-4 text-dark">
                    <span>Total</span>
                    <span style="color:#059669;">{{ number_format($this->grandTotal, 2) }} <span style="font-size:1rem;">SAR</span></span>
                </div>
                
                <button type="button" wire:click="placeOrder" class="btn btn-primary btn-lg w-100 fw-semibold" style="border-radius:12px;">
                    <span wire:loading.remove wire:target="placeOrder">Place Order</span>
                    <span wire:loading wire:target="placeOrder">Processing...</span>
                </button>

                <div class="text-center mt-3 text-muted" style="font-size:0.85rem;">
                    <i class="fa fa-lock"></i> Secure 256-bit SSL encryption
                </div>
            </div>
        </div>
    </div>
</div>
