<div>
    @if(count($cartItems) > 0)
        <div class="row g-5">
            {{-- Cart Items List --}}
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    @foreach($cartItems as $cartId => $details)
                        @php
                            $item = $itemsData[$details['id']] ?? null;
                        @endphp
                        @if($item)
                            @php
                                $currencyCode = $item->prices?->where('price_type','sell')->first()?->currency ?? 'SAR';
                                $unitPrice = $details['price'] ?? 0;
                                $itemName = $details['name'] ?? $item->getTranslation('name', 'en');
                                $itemDesc = $details['description'] ?? '';
                            @endphp
                            <div class="d-flex align-items-center gap-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                {{-- Image --}}
                                @if($item->primaryImage)
                                    <img src="{{ $item->primaryImage->url }}" alt="{{ $item->getTranslation('name','en') }}" class="rounded-3" style="width:80px; height:80px; object-fit:cover;">
                                @else
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-light text-muted" style="width:80px; height:80px; font-size:1.5rem;">
                                        @if($item->icon_class) <i class="{{ $item->icon_class }}"></i> @else &#129531; @endif
                                    </div>
                                @endif

                                {{-- Details --}}
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-1">{{ $itemName }}</h6>
                                    
                                    @if(!empty($itemDesc) && $itemDesc !== $itemName)
                                        <div class="text-muted mb-1" style="font-size:0.8rem;">
                                            {{ $itemDesc }}
                                        </div>
                                    @endif

                                    <div class="text-muted" style="font-size:0.9rem;">
                                        @if($unitPrice > 0)
                                            {{ number_format($unitPrice, 2) }} {{ $currencyCode }}
                                        @else
                                            Price on request
                                        @endif
                                    </div>
                                </div>

                                {{-- Quantity & Actions --}}
                                <div class="d-flex align-items-center gap-4">
                                    <div class="input-group" style="width:110px; border-radius:8px; overflow:hidden; border: 1px solid #e5e7eb;">
                                        <button class="btn btn-light border-0 px-2" wire:click="decrement('{{ $cartId }}')" type="button">&minus;</button>
                                        <input type="text" class="form-control text-center border-0 bg-white" value="{{ $details['quantity'] }}" readonly>
                                        <button class="btn btn-light border-0 px-2" wire:click="increment('{{ $cartId }}')" type="button">&#43;</button>
                                    </div>

                                    <div class="fw-bold" style="width:70px; text-align:right;">
                                        @if($unitPrice > 0)
                                            {{ number_format($unitPrice * $details['quantity'], 2) }}
                                        @endif
                                    </div>

                                    <button wire:click="removeItem('{{ $cartId }}')" class="btn btn-sm btn-outline-danger" style="border-radius:50%; width:32px; height:32px; padding:0;">
                                        &times;
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="col-lg-4">
                <div class="bg-white rounded-4 shadow-sm p-4 sticky-top" style="top:100px;">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Subtotal</span>
                        <span>{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4 fw-bold fs-5 text-dark">
                        <span>Total</span>
                        <span style="color:#059669;">{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <a href="{{ lroute('customer.checkout.index') }}" wire:navigate class="btn btn-primary btn-lg w-100 fw-semibold" style="border-radius:12px;">
                        Proceed to Checkout
                    </a>
                    <div class="text-center mt-3">
                        <a href="{{ lroute('catalog.index') }}" wire:navigate class="text-decoration-none text-muted" style="font-size:0.9rem;">
                            &larr; Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <div style="font-size:4rem; margin-bottom:1rem;">&#128722;</div>
            <h3 class="fw-bold text-dark">Your cart is empty</h3>
            <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
            <a href="{{ lroute('catalog.index') }}" wire:navigate class="btn btn-primary px-5 py-2" style="border-radius:12px;">
                Start Shopping
            </a>
        </div>
    @endif
</div>
