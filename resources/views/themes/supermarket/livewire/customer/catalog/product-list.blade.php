<div>
@php
    $iconVariants  = ['green','amber','purple','blue','rose','cyan'];
    $iconEmojis    = ['&#129531;','&#127800;','&#9889;','&#128167;','&#127807;','&#128717;'];
    $catIcons      = ['&#129531;','&#127800;','&#9889;','&#128167;','&#127807;','&#128717;','&#9989;','&#128666;'];
@endphp

{{-- ── Mobile category dropdown ──────────────────────────── --}}
<div class="bg-white border-bottom shadow-sm d-lg-none" style="padding: 12px 16px;">
    <div class="container-fluid px-0">
        <select wire:model.live="activeCategory" class="form-select form-select-lg" style="border-radius: 10px; font-size: 0.95rem; font-weight: 500; box-shadow: none; border-color: #e2e8f0;">
            <option value="">All Products ({{ $totalCount }})</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" style="font-weight: 600;">{{ $cat->name }} ({{ $cat->items_count }})</option>
                @foreach($cat->children as $subCat)
                    <option value="{{ $subCat->id }}">&nbsp;&nbsp;&nbsp;— {{ $subCat->name }} ({{ $subCat->items_count }})</option>
                @endforeach
            @endforeach
        </select>
    </div>
</div>

{{-- ── Main layout ─────────────────────────────────────── --}}
<div style="background:#f0fdf4; min-height:60vh; padding:48px 0 90px;">
    <div class="container-fluid">
        <div class="row g-4 align-items-start">

            {{-- ── Desktop Sidebar ── --}}
            <div class="col-lg-3 d-none d-lg-block">
                <div class="cat-sidebar bg-white rounded-4 p-3 shadow-sm">
                    <div class="cat-sidebar__title">Browse by Category</div>

                    <a href="#" wire:click.prevent="$set('activeCategory', '')"
                       class="cat-item {{ !$activeCategory ? 'active' : '' }}">
                        <span class="cat-item__icon">&#127775;</span>
                        <span>All Products</span>
                        <span class="cat-item__count">{{ $totalCount }}</span>
                    </a>


                    <div class="cat-sidebar-scroll" style="max-height: 55vh; overflow-y: auto; padding-right: 8px; margin-bottom: 1rem;">
                        @foreach($categories as $i => $cat)
                            <div class="cat-item-wrapper mb-1">
                                <div class="d-flex align-items-center">
                                    <a href="#" wire:click.prevent="$set('activeCategory', {{ $cat->id }})"
                                       class="cat-item flex-grow-1 {{ $activeCategory == $cat->id || (isset($activeMainCategoryId) && $activeMainCategoryId == $cat->id) ? 'active' : '' }}" style="border-radius: 8px 0 0 8px; margin-bottom: 0;">
                                        <span class="cat-item__icon">{!! $catIcons[$i % count($catIcons)] !!}</span>
                                        <span>{{ $cat->name }}</span>
                                        <span class="cat-item__count ms-auto me-2" style="position:static; margin-left:auto !important;">{{ $cat->items_count }}</span>
                                    </a>
                                    @if($cat->children->count() > 0)
                                        <button class="btn btn-sm text-muted bg-white border-0 {{ (isset($activeMainCategoryId) && $activeMainCategoryId == $cat->id) ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCat{{ $cat->id }}" aria-expanded="{{ (isset($activeMainCategoryId) && $activeMainCategoryId == $cat->id) ? 'true' : 'false' }}" style="border-radius: 0 8px 8px 0; padding: 12px 10px; background: transparent;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16" style="transition: transform 0.2s;">
                                                <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                
                                @if($cat->children->count() > 0)
                                    <div class="collapse {{ (isset($activeMainCategoryId) && $activeMainCategoryId == $cat->id) ? 'show' : '' }}" id="collapseCat{{ $cat->id }}">
                                        <div class="ps-4 pe-2 py-1 bg-light rounded-3 mt-1 mx-2">
                                            @foreach($cat->children as $subCat)
                                                <a href="#" wire:click.prevent="$set('activeCategory', {{ $subCat->id }})"
                                                   class="cat-item cat-item-sub {{ $activeCategory == $subCat->id ? 'active' : '' }} mb-1" style="font-size: 0.9rem; padding: 6px 10px; min-height: 38px;">
                                                    <span>{{ $subCat->name }}</span>
                                                    <span class="cat-item__count">{{ $subCat->items_count }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Promo block --}}
                    <div class="mt-4 p-3 rounded-3 text-center"
                         style="background: linear-gradient(135deg,#059669,#10b981);">
                        <div style="font-size:1.6rem; margin-bottom:6px;">&#127881;</div>
                        <div style="color:#fff; font-size:0.82rem; font-weight:700; line-height:1.4;">
                            First order<br>15% OFF
                        </div>
                        <a href="{{ lroute('customer.register') }}"
                           class="btn btn-light btn-sm mt-2 fw-bold px-3"
                           style="border-radius:8px; font-size:0.75rem;">
                            Claim Offer
                        </a>
                    </div>

                    {{-- Trust signals --}}
                    <div class="mt-3" style="border-top:1px solid #f1f3f5; padding-top:1rem;">
                        @foreach(['&#128666; Free Shipping over $35','&#10003; 30-Day Returns','&#127807; Eco-Friendly'] as $trust)
                            <div style="font-size:0.8rem; color:#6b7280; padding:5px 4px;">
                                {!! $trust !!}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── Products Grid ── --}}
            <div class="col-lg-9">
            
                {{-- Results and Sort Header --}}
                <div class="bg-white rounded-4 shadow-sm p-3 mb-4 d-flex flex-wrap gap-3 align-items-center justify-content-between">
                    <div class="text-muted" style="font-size: 0.95rem;">
                        Showing <strong class="text-dark">{{ $items->total() }}</strong>
                        {{ Str::plural('product', $items->total()) }}
                        @if($activeCategory)
                            in <strong class="text-dark">{{ $categories->firstWhere('id', $activeCategory)?->name }}</strong>
                        @endif
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        @if($activeCategory || $search)
                            <button type="button" wire:click="$set('activeCategory', ''); $set('search', '')"
                               class="btn btn-sm btn-outline-danger rounded-pill px-3"
                               style="font-size:0.8rem;">
                                &#10005; Clear filters
                            </button>
                        @endif
                        
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted d-none d-sm-inline" style="font-size:0.9rem;">Sort by:</span>
                            <select wire:model.live="sort" class="form-select form-select-sm" style="width:170px; border-radius:8px; border-color: #e2e8f0; box-shadow: none;">
                                <option value="name_asc">Name (A-Z)</option>
                                <option value="name_desc">Name (Z-A)</option>
                                <option value="price_asc">Price (Low to High)</option>
                                <option value="price_desc">Price (High to Low)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="position-relative">
                    <div wire:loading class="position-absolute w-100 h-100" style="background:rgba(255,255,255,0.7); z-index:10; border-radius:1rem;">
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>

                    @if($items->isEmpty())
                        <div class="catalog-empty bg-white rounded-4 shadow-sm">
                            <div class="catalog-empty__icon">&#129531;</div>
                            <h5 class="fw-bold text-dark mb-2">No products found</h5>
                            <p class="text-muted mb-4" style="font-size:0.9rem;">
                                Try a different category or search term.
                            </p>
                            <button type="button" wire:click="$set('activeCategory', ''); $set('search', '')" class="btn btn-primary px-4"
                               style="border-radius:10px;">Clear Filters</button>
                        </div>
                    @else
                        <div class="row g-4">
                            @foreach($items as $i => $item)
                                @php
                                    $variant = $iconVariants[$i % count($iconVariants)];
                                    $emoji   = $iconEmojis[$i % count($iconEmojis)];
                                @endphp
                                <div class="col-6 col-md-4 col-xl-3">
                                    @include('themes.supermarket.partials.product-card', [
                                        'item' => $item,
                                        'variant' => $variant,
                                        'emoji' => $emoji,
                                        'keyPrefix' => 'shop',
                                        'defaultCurrency' => $defaultCurrency
                                    ])
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if($items->hasPages())

                            <div class="mt-5 catalog-pagination-wrap d-flex justify-content-center">
                                {{ $items->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>
