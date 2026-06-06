        {{-- Dynamic Price --}}
        @php
            $sellPrice = $item->prices?->where('price_type','sell')->first();
            $currencyCode = $sellPrice ? $sellPrice->currency : 'SAR';
        @endphp

        @if($sellPrice)
            <div class="mb-4">
                <div style="font-size:2.2rem; font-weight:800; color:#064e3b; line-height:1;">
                    {{ number_format($this->unitPrice, 2) }}
                    <span style="font-size:1rem; font-weight:400; color:#6b7280;">{{ $currencyCode }}</span>
                </div>
                @if($quantity > 1)
                    <div class="text-muted mt-1" style="font-size:0.9rem;">
                        Subtotal: {{ number_format($this->totalPrice, 2) }} {{ $currencyCode }}
                    </div>
                @endif
                <div class="fw-semibold mt-1" style="font-size:0.85rem; color:#059669;">&#10003; Free shipping on orders over 35 {{ $currencyCode }}</div>
            </div>
        @else
            <div class="mb-4">
                <div class="text-muted fw-semibold">Price on request — contact us for a quote.</div>
            </div>
        @endif

        {{-- Variant Selector --}}
        @if ($item->activeVariants->count() > 0)
            <div class="mb-4">
                @foreach ($item->activeVariants->groupBy('attribute_id') as $attrId => $attrVariants)
                    @php
                        $attrName = $attrVariants->first()->attribute?->getTranslation('name', 'en') ?? 'Option';
                        $selectedVariantId = $selectedAttributes[$attrId] ?? null;
                    @endphp
                    <div class="mb-3">
                        <div class="text-uppercase text-muted fw-bold mb-2" style="font-size:0.75rem; letter-spacing:0.5px;">
                            {{ $attrName }}
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($attrVariants->sortBy('sort_order') as $variant)
                                <input type="radio" class="btn-check"
                                    name="attr-{{ $item->id }}-{{ $attrId }}-{{ $variant->id }}-{{ $modalId ?? 'main' }}"
                                    id="attr-{{ $item->id }}-{{ $attrId }}-{{ $variant->id }}-{{ $modalId ?? 'main' }}"
                                    autocomplete="off"
                                    {{ $selectedVariantId == $variant->id ? 'checked' : '' }}>
                                <label class="btn {{ $selectedVariantId == $variant->id ? 'btn-primary' : 'btn-outline-secondary' }} px-3 py-2"
                                    for="attr-{{ $item->id }}-{{ $attrId }}-{{ $variant->id }}-{{ $modalId ?? 'main' }}"
                                    wire:click="selectAttribute({{ $attrId }}, {{ $variant->id }})"
                                    style="border-radius:8px; font-size:0.9rem;">
                                    {{ $variant->getTranslation('name', 'en') }}
                                    @if ($variant->price_difference != 0)
                                        <span style="font-size:0.8rem; opacity:0.8;">
                                            ({{ $variant->price_difference > 0 ? '+' : '' }}{{ number_format($variant->price_difference, 2) }})
                                        </span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
        <div class="d-flex gap-2 flex-nowrap align-items-center w-100">
            {{-- Quantity Selector --}}
            <div class="input-group" style="width: 130px; border-radius: 12px; overflow:hidden; border: 1px solid #d1d5db;">
                <button class="btn btn-light border-0 px-2" type="button" wire:click.prevent="decrement" style="font-weight:bold; color:#374151; background:#f9fafb;">&minus;</button>
                <input type="text" class="form-control text-center border-0 bg-white shadow-none px-1" wire:model="quantity" readonly style="font-weight:600; color:#1f2937; font-size:0.9rem;">
                <button class="btn btn-light border-0 px-2" type="button" wire:click.prevent="increment" style="font-weight:bold; color:#374151; background:#f9fafb;">&#43;</button>
            </div>

            {{-- Add to Cart Button --}}
            <button type="button" wire:click.prevent="addToCart" class="btn btn-primary fw-semibold flex-grow-1 text-nowrap" style="border-radius: 12px; display:flex; align-items:center; justify-content:center; gap:4px; font-size: 0.9rem; padding: 0.6rem 1rem;">
                <span wire:loading.remove wire:target="addToCart"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cart-plus" viewBox="0 0 16 16"><path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9V5.5z"/><path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg></span>
                <span wire:loading wire:target="addToCart" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span>Add to Cart</span>
            </button>
        </div>
