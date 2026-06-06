<div class="col-lg-9 mt-4 mt-lg-0">
    <div class="row">
        <div class="col-lg-5 col-md-6">
            <div class="sticky-top" style="top:80px;">
                <div class="prd-main-img-wrap">
                    <button
                        type="button"
                        class="prd-main-img-button"
                        data-bs-toggle="modal"
                        data-bs-target="#productGalleryModal-{{ $product['id'] }}"
                        aria-label="Open product gallery"
                    >
                        <img
                            src="{{ $mainImage['url'] ?? $placeholder }}"
                            alt="{{ $mainImage['alt'] ?? $product['name'] }}"
                            style="max-height:480px;"
                        >
                        @if(count($galleryImages) > 1)
                            <span class="prd-gallery-badge">
                                <i class="icon icon-search"></i>
                                View Gallery
                            </span>
                        @endif
                    </button>
                </div>

                <div class="thumb-list">
                    @foreach($galleryImages as $i => $img)
                        <div
                            class="thumb-item {{ $i === $selectedImageIndex ? 'active' : '' }}"
                            wire:click="selectImage({{ $i }})"
                        >
                            <img src="{{ $img['url'] }}" alt="{{ $img['alt'] ?? $product['name'] }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-7 col-md-6 mt-4 mt-md-0 tf-product-info-wrap">
            <div class="tf-product-info-list">
                @if($product['brand_id'])
                    <div class="mb_8">
                        <a
                            href="{{ route('shop', ['brand' => $product['brand_id']]) }}"
                            style="font-size:12px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:1px;text-decoration:none;"
                        >
                            {{ $product['brand_name'] }}
                        </a>
                    </div>
                @endif

                <h1 style="font-size:22px;font-weight:700;color:#1a1a1a;line-height:1.35;margin-bottom:16px;">
                    {{ $product['name'] }}
                </h1>

                <div style="margin-bottom:4px;display:flex;align-items:baseline;flex-wrap:wrap;gap:6px;">
                    <span class="prd-price-main">{{ $fmtPrice }}</span>
                    @if($fmtCompare)
                        <span class="prd-price-compare">{{ $fmtCompare }}</span>
                    @endif
                    @if($discount)
                        <span class="prd-discount-badge">{{ $discount }}% OFF</span>
                    @endif
                </div>
                <div class="prd-vat">VAT Included</div>

                <div class="mt_10 mb_6">
                    <span class="{{ $stockClass }}">{{ $stockText }}</span>
                </div>

                @if($product['short_description'])
                    <div style="font-size:13px;color:#0a5fa0;line-height:1.7;margin:14px 0;">
                        {{ $product['short_description'] }}
                    </div>
                @endif

                <hr style="border:none;border-top:1px solid #eee;margin:18px 0;">

                @if(($product['has_variants'] ?? false) && count($variantAttributes))
                    @foreach($variantAttributes as $attribute)
                        <div class="variant-group">
                            <span class="variant-group-label">{{ $attribute['name'] }}</span>
                            <div class="variant-group-values">
                                @foreach($attribute['values'] as $value)
                                    @php
                                        $attrId = (int) $attribute['id'];
                                        $valId = (int) $value['id'];
                                        $isActive = ((int) ($selectedByAttr[$attrId] ?? 0) === $valId) ? 'active' : '';
                                        $isAvailable = (bool) ($availability[$attrId][$valId] ?? false);
                                        $disabledStyle = $isAvailable ? '' : 'opacity:.45;';
                                    @endphp

                                    @if(($attribute['type'] ?? '') === 'color')
                                        <div
                                            class="swatch-color-tile {{ $isActive }}"
                                            title="{{ $value['value'] }}"
                                            style="{{ $disabledStyle }}"
                                            wire:click="selectAttributeValue({{ $attrId }}, {{ $valId }})"
                                        >
                                            <span class="swatch-inner" style="background-color:{{ $value['hex_code'] ?: '#ccc' }};"></span>
                                        </div>
                                    @elseif(($attribute['type'] ?? '') === 'image')
                                        <div
                                            class="swatch-img-tile {{ $isActive }}"
                                            title="{{ $value['value'] }}"
                                            style="{{ $disabledStyle }}"
                                            wire:click="selectAttributeValue({{ $attrId }}, {{ $valId }})"
                                        >
                                            @if($value['image_url'])
                                                <img src="{{ $value['image_url'] }}" alt="{{ $value['value'] }}">
                                            @else
                                                <span style="font-size:10px;color:#999;text-align:center;padding:2px;">{{ $value['value'] }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <div
                                            class="text-swatch {{ $isActive }}"
                                            style="{{ $disabledStyle }}"
                                            wire:click="selectAttributeValue({{ $attrId }}, {{ $valId }})"
                                        >
                                            {{ $value['value'] }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    <hr style="border:none;border-top:1px solid #eee;margin:18px 0;">
                @endif

                <div class="tf-product-total-quantity">
                    <div class="group-btn wrap-quantity mb_16">
                        <div>
                            <div class="text-body-default mb_15">
                                Quantity:
                                <span class="variant-picker-label-value value-currentQuantity fw-7">{{ $quantity }}</span>
                            </div>
                            <div class="wg-quantity">
                                <button class="btn-quantity btn-decrease" type="button" wire:click="decrementQuantity">
                                    <i class="icon icon-minus"></i>
                                </button>
                                <input class="quantity-product" type="number" min="1" wire:model.live="quantity">
                                <button class="btn-quantity btn-increase" type="button" wire:click="incrementQuantity">
                                    <i class="icon icon-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="group-btn flex-md-nowrap mb_10">
                        <a href="#shoppingCart" data-bs-toggle="modal" class="tf-btn btn-bg-primary w-full btn-add-to-cart btn-h-52 animate-btn">
                            Add to Cart
                        </a>
                    </div>
                </div>

                <div class="trust-badges">
                    <div class="trust-badge">
                        <i class="badge-icon icon-shipping"></i>
                        <div>
                            <div class="badge-value">3-5 Days</div>
                            <div class="badge-label">Delivery</div>
                        </div>
                    </div>
                    @if($product['warranty_months'])
                        <div class="trust-badge">
                            <i class="badge-icon icon-support"></i>
                            <div>
                                <div class="badge-value">{{ $product['warranty_months'] }} Mo.</div>
                                <div class="badge-label">Warranty</div>
                            </div>
                        </div>
                    @endif
                    <div class="trust-badge">
                        <i class="badge-icon icon-box-return"></i>
                        <div>
                            <div class="badge-value">14 Days</div>
                            <div class="badge-label">Returns</div>
                        </div>
                    </div>
                </div>

                <div style="margin-top:18px;font-size:13px;color:#888;line-height:2.2;">
                    @php
                        $sku = $variant['sku'] ?? null;
                        $fallbackSku = $product['reference'] ?? null;
                    @endphp
                    @if($sku || $fallbackSku)
                        <div>SKU: <span style="color:#444;font-weight:600;">{{ $sku ?: $fallbackSku }}</span></div>
                    @endif
                    @if($product['category_id'])
                        <div>
                            Category:
                            <a href="{{ route('shop', ['category' => $product['category_id']]) }}" style="color:#0a5fa0;font-weight:600;">
                                {{ $product['category_name'] }}
                            </a>
                        </div>
                    @endif
                    @if($product['model_number'])
                        <div>Model: <span style="color:#444;font-weight:600;">{{ $product['model_number'] }}</span></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div
        class="modal fade"
        id="productGalleryModal-{{ $product['id'] }}"
        tabindex="-1"
        aria-labelledby="productGalleryModalLabel-{{ $product['id'] }}"
        aria-hidden="true"
        wire:ignore.self
    >
        <div class="modal-dialog modal-dialog-centered modal-xl prd-gallery-modal-dialog">
            <div class="modal-content prd-gallery-modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="productGalleryModalLabel-{{ $product['id'] }}">
                        {{ $product['name'] }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body pt-2 prd-gallery-modal-body">
                    <div class="prd-gallery-modal-stage prd-zoom-surface">
                        @if(count($galleryImages) > 1)
                            <button type="button" class="prd-gallery-nav prd-gallery-nav-prev" wire:click="previousImage" aria-label="Previous image">
                                <i class="icon icon-caret-left"></i>
                            </button>
                        @endif

                        <img
                            src="{{ $mainImage['url'] ?? $placeholder }}"
                            alt="{{ $mainImage['alt'] ?? $product['name'] }}"
                            class="prd-gallery-modal-image prd-zoom-target"
                        >

                        @if(count($galleryImages) > 1)
                            <button type="button" class="prd-gallery-nav prd-gallery-nav-next" wire:click="nextImage" aria-label="Next image">
                                <i class="icon icon-caret-right"></i>
                            </button>
                        @endif
                    </div>

                    @if(count($galleryImages) > 1)
                        <div class="thumb-list justify-content-center mt-3">
                            @foreach($galleryImages as $i => $img)
                                <div
                                    class="thumb-item {{ $i === $selectedImageIndex ? 'active' : '' }}"
                                    wire:click="selectImage({{ $i }})"
                                >
                                    <img src="{{ $img['url'] }}" alt="{{ $img['alt'] ?? $product['name'] }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
