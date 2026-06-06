<div>
    <livewire:customer.layout.navigation />

    {{-- ── Page header ─────────────────────────────────────── --}}
    <div
        style="background:linear-gradient(135deg,#0a2463 0%,#1565c0 60%,#0d6efd 100%); padding:36px 0 56px; position:relative; overflow:hidden;">
        <div
            style="position:absolute;bottom:-40px;left:0;right:0;height:60px;background:#f0f4f8;clip-path:ellipse(55% 100% at 50% 100%);">
        </div>
        <div class="container position-relative" style="z-index:2;">
            @include('themes.laundry-one.partials.breadcrumb', [
                'variant' => 'light',
                'items' => [
                    ['label' => 'Order'],
                ],
            ])
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <div class="catalog-header__count">&#128717; Order Builder</div>
                    <h1 style="font-size:clamp(1.5rem,3vw,2.2rem);font-weight:800;color:#fff;margin:0.4rem 0 0.3rem;">
                        Build Your Laundry Order
                    </h1>
                    <p style="color:rgba(255,255,255,0.75);font-size:0.93rem;margin:0;">
                        Pick services, set quantities, add extras — we handle the rest.
                    </p>
                </div>
                @if ($this->cartCount > 0)
                    <div class="ob-cart-summary-pill d-none d-lg-flex">
                        <span>&#128717;</span>
                        <span>{{ $this->cartCount }} {{ Str::plural('item', $this->cartCount) }}</span>
                        <strong>{{ number_format($this->grandTotal, 2) }} SAR</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Mobile cart toggle button ───────────────────────── --}}
    @if ($this->cartCount > 0)
        <div class="d-lg-none ob-mobile-bar" wire:click="$set('showCart', true)">
            <div class="d-flex align-items-center gap-2">
                <span class="ob-mobile-bar__badge">{{ $this->cartCount }}</span>
                <span>View Your Order</span>
            </div>
            <strong>{{ number_format($this->grandTotal, 2) }} SAR &rsaquo;</strong>
        </div>
    @endif

    {{-- ── Main layout ─────────────────────────────────────── --}}
    <div style="background:#f0f4f8; min-height:80vh; padding:32px 0 80px;">
        <div class="container">
            <div class="row g-4 align-items-start">

                {{-- ════════════════ LEFT: Service Browser ════════════ --}}
                <div class="col-lg-7">

                    {{-- Category pills --}}
                    <div class="ob-cat-bar">
                        <button wire:click="$set('activeCategory','')"
                            class="ob-cat-pill {{ $activeCategory === '' ? 'active' : '' }}">
                            &#127775; All
                        </button>
                        @foreach ($categories as $i => $cat)
                            <button wire:click="$set('activeCategory','{{ $cat->id }}')"
                                class="ob-cat-pill {{ $activeCategory == $cat->id ? 'active' : '' }}">
                                {{ $cat->name }}
                                <span class="ob-cat-pill__count">{{ $cat->items_count }}</span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Search --}}
                    <div class="input-group mt-3">
                        <span class="input-group-text bg-white border-end-0">&#128269;</span>
                        <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search services..."
                            class="form-control border-start-0 ps-0" autocomplete="off">
                        @if ($search)
                            <button class="btn btn-outline-secondary" wire:click="$set('search','')"
                                type="button">&#10005;</button>
                        @endif
                    </div>

                    {{-- Service list --}}
                    <div class="d-flex flex-column gap-3 mt-3">
                        @forelse($services as $i => $service)
                            <?php
                            $inCart = isset($this->cart[$service->id]);
                            $qty = $this->cart[$service->id]['qty'] ?? 0;
                            $price = $service->prices->where('price_type', 'sell')->first();
                            $colors = ['#e8f0fe', '#f3e8ff', '#fff3e8', '#e8fff3', '#ffe8e8', '#e8f7ff'];
                            $fgColors = ['#1d4ed8', '#7c3aed', '#c2410c', '#15803d', '#dc2626', '#0891b2'];
                            $bg = $colors[$i % count($colors)];
                            $fg = $fgColors[$i % count($fgColors)];
                            ?>

                            <div class="ob-service-row {{ $inCart ? 'ob-service-row--active' : '' }}">

                                {{-- Icon / image --}}
                                <div class="ob-service-row__thumb"
                                    style="background:{{ $bg }}; color:{{ $fg }};">
                                    @if ($service->primaryImage)
                                        <img src="{{ $service->primaryImage->url }}"
                                            alt="{{ $service->getTranslation('name', 'en') }}"
                                            style="width:100%;height:100%;object-fit:cover;border-radius:14px;">
                                    @elseif($service->icon_class)
                                        <i class="{{ $service->icon_class }}" style="font-size:1.5rem;"></i>
                                    @else
                                        &#128107;
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="ob-service-row__info">
                                    @if ($service->category)
                                        <div class="ob-service-row__cat">{{ $service->category->name }}</div>
                                    @endif
                                    <div class="ob-service-row__name">{{ $service->getTranslation('name', 'en') }}</div>
                                    @if ($service->short_description)
                                        <div class="ob-service-row__desc">
                                            {!! \Illuminate\Support\Str::limit(strip_tags($service->short_description), 75) !!}
                                        </div>
                                    @endif
                                </div>

                                {{-- Price + controls --}}
                                <div class="ob-service-row__controls">
                                    <div class="ob-service-row__price">
                                        @php
                                            $basePrice = $service->prices->where('price_type', 'sell')->first();
                                            $displayPrice = $basePrice ? $basePrice->price : 0;
                                        @endphp
                                        @if ($service->activeVariants->count() > 0)
                                            from {{ number_format($displayPrice, 2) }}
                                            <span>SAR</span>
                                        @elseif($basePrice)
                                            {{ number_format($displayPrice, 2) }}
                                            <span>{{ $basePrice->currency }}</span>
                                        @else
                                            <span style="font-size:0.75rem;color:#9ca3af;">On request</span>
                                        @endif
                                    </div>

                                    @if ($inCart)
                                        <div class="ob-qty">
                                            <button class="ob-qty__btn" wire:click="decrement({{ $service->id }})"
                                                type="button">−</button>
                                            <span class="ob-qty__val">{{ $qty }}</span>
                                            <button class="ob-qty__btn ob-qty__btn--add"
                                                wire:click="increment({{ $service->id }})" type="button">+</button>
                                        </div>
                                    @else
                                        <button class="ob-add-btn" wire:click="add({{ $service->id }})"
                                            type="button">
                                            + Add
                                        </button>
                                    @endif
                                </div>
                            </div>

                        @empty
                            <div class="text-center py-5 text-muted">
                                <div style="font-size:2.5rem;margin-bottom:0.75rem;">&#128107;</div>
                                No services found in this category.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- ════════════════ RIGHT: Order Summary ════════════ --}}
                <div class="col-lg-5">
                    <div class="ob-cart {{ $showCart ? 'ob-cart--open' : '' }}">

                        {{-- Mobile close --}}
                        <button class="ob-cart__close d-lg-none" wire:click="$set('showCart',false)" type="button">
                            &#10005;
                        </button>

                        {{-- Header --}}
                        <div class="ob-cart__header">
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size:1.2rem;">&#128717;</span>
                                <h5 class="mb-0" style="font-weight:700;color:#0a2463;">Your Order</h5>
                                @if ($this->cartCount > 0)
                                    <span class="ob-cart__badge">{{ $this->cartCount }}</span>
                                @endif
                            </div>
                            @if ($this->cartCount > 0)
                                <button class="ob-clear-btn" wire:click="clearCart" type="button"
                                    wire:confirm="Clear all items from your order?">
                                    Clear all
                                </button>
                            @endif
                        </div>

                        {{-- Empty state --}}
                        @if ($this->cartCount === 0)
                            <div class="ob-cart__empty">
                                <div style="font-size:3rem;margin-bottom:0.75rem;">&#128230;</div>
                                <div style="font-weight:600;color:#374151;margin-bottom:0.3rem;">Your order is empty
                                </div>
                                <div style="font-size:0.85rem;color:#9ca3af;">Add services from the left to get started.
                                </div>
                            </div>
                        @else
                            {{-- Cart items --}}
                            <div class="ob-cart__items">
                                @foreach ($this->cartServices as $id => $service)
                                    <?php
                                    $entry      = $this->cart[$id];
                                    $sellPrice  = $service->prices->where('price_type', 'sell')->first();
                                    $basePrice  = (float) ($sellPrice?->price ?? 0);
                                    $priceDiff  = collect($entry['attributes'] ?? [])->sum(
                                        fn ($vid) => (float) ($service->activeVariants->firstWhere('id', $vid)?->price_difference ?? 0)
                                    );
                                    $unitPrice  = max(0, $basePrice + $priceDiff);
                                    $lineAmt    = $unitPrice * $entry['qty'];
                                    ?>

                                    <div class="ob-cart-item">
                                        {{-- Item row --}}
                                        <div class="ob-cart-item__row">
                                            <div class="ob-cart-item__name">
                                                {{ $service->getTranslation('name', 'en') }}
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="ob-qty ob-qty--sm">
                                                    <button class="ob-qty__btn"
                                                        wire:click="decrement({{ $id }})"
                                                        type="button">−</button>
                                                    <span class="ob-qty__val">{{ $entry['qty'] }}</span>
                                                    <button class="ob-qty__btn ob-qty__btn--add"
                                                        wire:click="increment({{ $id }})"
                                                        type="button">+</button>
                                                </div>
                                                <button class="ob-cart-item__remove"
                                                    wire:click="remove({{ $id }})" type="button">
                                                    &#10005;
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Line price --}}
                                        <div class="ob-cart-item__price-row">
                                            <span style="font-size:0.78rem;color:#9ca3af;">
                                                {{ number_format($unitPrice, 2) }} × {{ $entry['qty'] }}
                                            </span>
                                            <span style="font-weight:700;color:#0a2463;font-size:0.9rem;">
                                                {{ number_format($lineAmt, 2) }} SAR
                                            </span>
                                        </div>

                                        {{-- Variant options grouped by attribute --}}
                                        @if ($service->activeVariants->count() > 0)
                                            <div class="mt-2">
                                                @foreach ($service->activeVariants->groupBy('attribute_id') as $attrId => $attrVariants)
                                                    @php
                                                        $attrName = $attrVariants->first()->attribute?->getTranslation('name', 'en') ?? 'Option';
                                                        $selectedVariantId = $this->cart[$service->id]['attributes'][$attrId] ?? null;
                                                    @endphp
                                                    <div class="mb-2">
                                                        <div class="text-uppercase text-muted fw-bold mb-1"
                                                            style="font-size:0.68rem;letter-spacing:.6px;">
                                                            {{ $attrName }}
                                                        </div>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach ($attrVariants->sortBy('sort_order') as $variant)
                                                                <input type="radio" class="btn-check"
                                                                    name="attr-{{ $service->id }}-{{ $attrId }}"
                                                                    id="attr-{{ $service->id }}-{{ $attrId }}-{{ $variant->id }}"
                                                                    autocomplete="off"
                                                                    {{ $selectedVariantId == $variant->id ? 'checked' : '' }}>
                                                                <label class="ob-variant-btn"
                                                                    for="attr-{{ $service->id }}-{{ $attrId }}-{{ $variant->id }}"
                                                                    wire:click="selectAttribute({{ $service->id }}, {{ $attrId }}, {{ $variant->id }})">
                                                                    <span style="font-size:11px;">
                                                                        {{ $variant->getTranslation('name', 'en') }}
                                                                    </span>
                                                                    @if ($variant->price_difference != 0)
                                                                        <span class="ob-variant-price" style="font-size:10px;">
                                                                            {{ $variant->price_difference > 0 ? '+' : '' }}{{ number_format($variant->price_difference, 2) }}
                                                                        </span>
                                                                    @endif
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Note --}}
                            <div class="ob-cart__note">
                                <label
                                    style="font-size:0.8rem;font-weight:600;color:#374151;margin-bottom:4px;display:block;">
                                    &#128221; Special instructions (optional)
                                </label>
                                <textarea wire:model.lazy="note" rows="2"
                                    placeholder="e.g. handle delicates with care, no fabric softener on silk..."
                                    style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:8px 12px;font-size:0.85rem;resize:none;outline:none;color:#374151;background:#fafafa;"></textarea>
                            </div>

                            {{-- Delivery method --}}
                            <div class="ob-delivery">
                                <div class="ob-delivery__label">&#128666; Delivery Method</div>
                                @foreach ($deliveryMethods as $method)
                                    <label
                                        class="ob-delivery__option {{ $deliveryMethodId == $method->id ? 'ob-delivery__option--active' : '' }}">
                                        <input type="radio" class="ob-delivery__radio" name="delivery_method"
                                            wire:click="$set('deliveryMethodId', {{ $method->id }})"
                                            {{ $deliveryMethodId == $method->id ? 'checked' : '' }}>
                                        <span class="ob-delivery__icon">{{ $method->icon }}</span>
                                        <span class="ob-delivery__info">
                                            <span class="ob-delivery__name">{{ $method->name }}</span>
                                            <span class="ob-delivery__desc">{{ $method->description }} &middot;
                                                {{ $method->estimated_label }}</span>
                                        </span>
                                        <span class="ob-delivery__price">
                                            {{ $method->price > 0 ? '+' . number_format($method->price, 2) . ' SAR' : 'Free' }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            {{-- Totals --}}
                            <div class="ob-totals">
                                <div class="ob-totals__row">
                                    <span>Services subtotal</span>
                                    <span>{{ number_format($this->subtotal, 2) }} SAR</span>
                                </div>
                                @if ($this->deliveryPrice > 0)
                                    <div class="ob-totals__row">
                                        <span>Delivery</span>
                                        <span>{{ number_format($this->deliveryPrice, 2) }} SAR</span>
                                    </div>
                                @endif
                                <div class="ob-totals__row ob-totals__row--total">
                                    <span>Total</span>
                                    <span>{{ number_format($this->grandTotal, 2) }} SAR</span>
                                </div>
                            </div>

                            {{-- Review & Place order --}}
                            @auth
                                <button type="button" wire:click="$set('showReviewModal', true)" class="ob-place-btn">
                                    &#128203; Review Order
                                </button>
                                <div style="text-align:center;font-size:0.75rem;color:#9ca3af;margin-top:8px;">
                                    &#128272; Secure checkout &nbsp;&middot;&nbsp; Free pickup included
                                </div>
                            @else
                                <a href="{{ route('customer.login') }}" wire:navigate class="ob-place-btn"
                                    style="text-decoration:none;display:block;text-align:center;">
                                    Sign in to Place Order
                                </a>
                                <div style="text-align:center;font-size:0.78rem;color:#9ca3af;margin-top:8px;">
                                    Don't have an account?
                                    <a href="{{ route('customer.register') }}" wire:navigate
                                        style="color:#0d6efd;font-weight:600;">Sign up free</a>
                                </div>
                            @endauth

                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Mobile cart overlay backdrop --}}
    @if ($showCart)
        <div class="ob-backdrop d-lg-none" wire:click="$set('showCart',false)"></div>
    @endif

    {{-- ── Review Order Modal ───────────────────────────────── --}}
    @if ($showReviewModal)
        <div class="ob-modal-backdrop" wire:click.self="$set('showReviewModal', false)">
            <div class="ob-modal">

                {{-- Header --}}
                <div class="ob-modal__header">
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:1.2rem;">&#128203;</span>
                        <h5 class="mb-0 fw-bold" style="color:#0a2463;">Review Your Order</h5>
                    </div>
                    <button class="btn-close" wire:click="$set('showReviewModal', false)" type="button"></button>
                </div>

                {{-- Items --}}
                <div class="ob-modal__body">
                    <div class="fw-bold text-uppercase mb-2"
                        style="font-size:0.72rem;letter-spacing:.7px;color:#9ca3af;">Items</div>
                    @foreach ($this->cartServices as $id => $service)
                        <?php
                        $entry = $this->cart[$id];
                        $attributeIds = $entry['attributes'] ?? [];
                        $basePrice = $service->prices->where('price_type', 'sell')->first();
                        $unitPrice = (float) ($basePrice?->price ?? 0);

                        // Add attribute price differences
                        foreach ($attributeIds as $variantId) {
                            $variant = $service->variants->find($variantId);
                            if ($variant) {
                                $unitPrice += (float) $variant->price_difference;
                            }
                        }

                        $lineAmt = $unitPrice * $entry['qty'];
                        ?>
                        <div class="d-flex justify-content-between align-items-start py-2 border-bottom">
                            <div>
                                <div class="fw-semibold" style="color:#0a2463;font-size:0.9rem;">
                                    {{ $service->getTranslation('name', 'en') }}
                                </div>
                                @if (!empty($attributeIds))
                                    @php
                                        $attrParts = [];
                                        foreach ($service->activeVariants->groupBy('attribute_id') as $attrId => $attrVariants) {
                                            if (isset($attributeIds[$attrId])) {
                                                $variant = $attrVariants->firstWhere('id', $attributeIds[$attrId]);
                                                if ($variant) {
                                                    $attrParts[] = $variant->getTranslation('name', 'en');
                                                }
                                            }
                                        }
                                    @endphp
                                    <div class="text-muted" style="font-size:0.78rem;">
                                        {{ implode(', ', $attrParts) }}
                                    </div>
                                @endif
                                <div class="text-muted" style="font-size:0.78rem;">
                                    {{ number_format($unitPrice, 2) }} SAR &times; {{ $entry['qty'] }}
                                </div>
                            </div>
                            <div class="fw-bold" style="color:#0a2463;white-space:nowrap;">
                                {{ number_format($lineAmt, 2) }} SAR
                            </div>
                        </div>
                    @endforeach

                    {{-- Delivery --}}
                    @if ($this->selectedDelivery)
                        <div class="mt-3 mb-1 fw-bold text-uppercase"
                            style="font-size:0.72rem;letter-spacing:.7px;color:#9ca3af;">Delivery</div>
                        <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background:#f0f4f8;">
                            <span style="font-size:1.3rem;">{{ $this->selectedDelivery->icon }}</span>
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size:0.88rem;color:#0a2463;">
                                    {{ $this->selectedDelivery->name }}</div>
                                <div class="text-muted" style="font-size:0.76rem;">
                                    {{ $this->selectedDelivery->estimated_label }}</div>
                            </div>
                            <div class="fw-bold" style="color:#0d6efd;font-size:0.88rem;">
                                {{ $this->deliveryPrice > 0 ? number_format($this->deliveryPrice, 2) . ' SAR' : 'Free' }}
                            </div>
                        </div>
                    @endif

                    {{-- Note --}}
                    @if ($note)
                        <div class="mt-3 mb-1 fw-bold text-uppercase"
                            style="font-size:0.72rem;letter-spacing:.7px;color:#9ca3af;">Note</div>
                        <div class="text-muted p-2 rounded-3" style="background:#f0f4f8;font-size:0.83rem;">
                            {{ $note }}</div>
                    @endif

                    {{-- Totals --}}
                    <div class="mt-3 pt-2 border-top">
                        <div class="d-flex justify-content-between mb-1" style="font-size:0.85rem;color:#6c757d;">
                            <span>Services subtotal</span>
                            <span>{{ number_format($this->subtotal, 2) }} SAR</span>
                        </div>
                        @if ($this->deliveryPrice > 0)
                            <div class="d-flex justify-content-between mb-1" style="font-size:0.85rem;color:#6c757d;">
                                <span>Delivery</span>
                                <span>{{ number_format($this->deliveryPrice, 2) }} SAR</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mt-2">
                            <span class="fw-bold" style="font-size:1rem;">Total</span>
                            <span class="fw-bold"
                                style="font-size:1.1rem;color:#0d6efd;">{{ number_format($this->grandTotal, 2) }}
                                SAR</span>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="ob-modal__footer">
                    <button type="button" class="btn btn-outline-secondary"
                        wire:click="$set('showReviewModal', false)">
                        &#8592; Edit Order
                    </button>
                    <button type="button" class="btn btn-primary fw-semibold px-4" wire:click="placeOrder"
                        wire:loading.attr="disabled" wire:target="placeOrder">
                        <span wire:loading wire:target="placeOrder" class="spinner-border spinner-border-sm me-1"
                            role="status"></span>
                        <span wire:loading.remove wire:target="placeOrder">&#10003;</span>
                        Confirm & Place Order
                    </button>
                </div>

            </div>
        </div>
    @endif

    @include(theme_view('partials.footer'))

    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

</div>
