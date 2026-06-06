@extends('themes.laundry-one.layouts.guest')

@section('content')

<livewire:customer.layout.navigation />

@php
    $iconVariants  = ['blue','purple','orange','green','red','cyan'];
    $iconEmojis    = ['&#128107;','&#129309;','&#129455;','&#128336;','&#128098;','&#128717;'];
    $catIcons      = ['&#128107;','&#129309;','&#129455;','&#128336;','&#128098;','&#128717;','&#9989;','&#128666;'];
@endphp

{{-- ── Header ─────────────────────────────────────────── --}}
<div class="catalog-header">
    <div class="container position-relative" style="z-index:2; text-align:center;">
        @include('themes.laundry-one.partials.breadcrumb', [
            'variant' => 'light',
            'items' => [
                ['label' => 'Services'],
            ],
        ])
        <div class="catalog-header__count">
            &#10024; {{ $totalCount }} {{ Str::plural('Service', $totalCount) }} Available
        </div>
        <h1 class="hero-title mb-3" style="font-size:clamp(1.9rem,4vw,3rem);">
            What Can We<br><span>Clean for You?</span>
        </h1>
        <p class="hero-subtitle mx-auto" style="font-size:1rem; max-width:460px;">
            From everyday washing to specialist treatments — we handle every fabric with professional care.
        </p>
    </div>
</div>

{{-- ── Mobile category strip ──────────────────────────── --}}
<div class="bg-white border-bottom shadow-sm" style="padding:0 16px;">
    <div class="container">
        <div class="cat-scroll-strip">
            <a href="{{ route('catalog.index') }}"
               class="cat-pill-mob {{ !$activeCategory ? 'active' : '' }}">
                &#127775; All
                <span style="font-size:0.72rem; opacity:0.75;">({{ $totalCount }})</span>
            </a>
            @foreach($categories as $i => $cat)
                <a href="{{ route('catalog.index', ['category' => $cat->id]) }}"
                   class="cat-pill-mob {{ $activeCategory == $cat->id ? 'active' : '' }}">
                    {!! $catIcons[$i % count($catIcons)] !!} {{ $cat->name }}
                    <span style="font-size:0.72rem; opacity:0.75;">({{ $cat->items_count }})</span>
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Main layout ─────────────────────────────────────── --}}
<div style="background:#f0f4f8; min-height:60vh; padding:48px 0 90px;">
    <div class="container">
        <div class="row g-4 align-items-start">

            {{-- ── Desktop Sidebar ── --}}
            <div class="col-lg-3">
                <div class="cat-sidebar bg-white rounded-4 p-3 shadow-sm">
                    <div class="cat-sidebar__title">Browse by Category</div>

                    <a href="{{ route('catalog.index') }}"
                       class="cat-item {{ !$activeCategory ? 'active' : '' }}">
                        <span class="cat-item__icon">&#127775;</span>
                        <span>All Services</span>
                        <span class="cat-item__count">{{ $totalCount }}</span>
                    </a>

                    @foreach($categories as $i => $cat)
                        <a href="{{ route('catalog.index', ['category' => $cat->id]) }}"
                           class="cat-item {{ $activeCategory == $cat->id ? 'active' : '' }}">
                            <span class="cat-item__icon">{!! $catIcons[$i % count($catIcons)] !!}</span>
                            <span>{{ $cat->name }}</span>
                            <span class="cat-item__count">{{ $cat->items_count }}</span>
                        </a>
                    @endforeach

                    {{-- Promo block --}}
                    <div class="mt-4 p-3 rounded-3 text-center"
                         style="background: linear-gradient(135deg,#0d6efd,#0dcaf0);">
                        <div style="font-size:1.6rem; margin-bottom:6px;">&#127881;</div>
                        <div style="color:#fff; font-size:0.82rem; font-weight:700; line-height:1.4;">
                            First order<br>20% OFF
                        </div>
                        <a href="{{ route('customer.register') }}"
                           class="btn btn-light btn-sm mt-2 fw-bold px-3"
                           style="border-radius:8px; font-size:0.75rem;">
                            Claim Offer
                        </a>
                    </div>

                    {{-- Trust signals --}}
                    <div class="mt-3" style="border-top:1px solid #f1f3f5; padding-top:1rem;">
                        @foreach(['&#128666; Free Pickup','&#10003; Satisfaction Guaranteed','&#9889; Fast Turnaround'] as $trust)
                            <div style="font-size:0.8rem; color:#6c757d; padding:5px 4px;">
                                {!! $trust !!}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── Services Grid ── --}}
            <div class="col-lg-9">

                {{-- Results header --}}
                <div class="catalog-results-header">
                    <div class="catalog-results-count">
                        Showing <strong>{{ $services->count() }}</strong>
                        {{ Str::plural('service', $services->count()) }}
                        @if($activeCategory)
                            in <strong>{{ $categories->firstWhere('id', $activeCategory)?->name }}</strong>
                        @endif
                    </div>
                    @if($activeCategory)
                        <a href="{{ route('catalog.index') }}"
                           class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                           style="font-size:0.8rem;">
                            &#10005; Clear filter
                        </a>
                    @endif
                </div>

                @if($services->isEmpty())
                    <div class="catalog-empty bg-white rounded-4 shadow-sm">
                        <div class="catalog-empty__icon">&#128107;</div>
                        <h5 class="fw-bold text-dark mb-2">No services in this category</h5>
                        <p class="text-muted mb-4" style="font-size:0.9rem;">
                            Try a different category or browse all our services.
                        </p>
                        <a href="{{ route('catalog.index') }}" class="btn btn-primary px-4"
                           style="border-radius:10px;">View All Services</a>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($services as $i => $service)
                            @php
                                $sellPrice    = $service->prices->where('price_type','sell')->first();
                                $currencyCode = $defaultCurrency?->code ?? $sellPrice?->currency ?? '';
                                $currencySymbol = $defaultCurrency?->symbol_left ?? $defaultCurrency?->symbol_right ?? '';
                                $variant      = $iconVariants[$i % count($iconVariants)];
                                $emoji        = $iconEmojis[$i % count($iconEmojis)];
                                $detailUrl    = $service->slug ? route('catalog.show', $service->slug) : null;
                            @endphp

                            <div class="col-sm-6 col-xl-4">
                                @if($detailUrl)
                                    <a href="{{ $detailUrl }}" class="svc-card d-block">
                                @else
                                    <div class="svc-card">
                                @endif

                                {{-- Image area --}}
                                <div class="svc-card__img-wrap">
                                    @if($service->primaryImage)
                                        <img src="{{ $service->primaryImage->url }}"
                                             alt="{{ $service->getTranslation('name','en') }}">
                                    @else
                                        <div class="svc-card__no-img svc-card__no-img--{{ $variant }}">
                                            @if($service->icon_class)
                                                <i class="{{ $service->icon_class }}"
                                                   style="font-size:3.5rem;"></i>
                                            @else
                                                {!! $emoji !!}
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Price badge --}}
                                    @if($sellPrice)
                                        <div class="svc-card__price-badge">
                                            {{ $currencySymbol }}{{ number_format($sellPrice->price, 2) }} {{ $currencyCode }}
                                        </div>
                                    @else
                                        <div class="svc-card__price-badge" style="background:rgba(22,163,74,0.85);">
                                            On request
                                        </div>
                                    @endif

                                    {{-- Category badge on image --}}
                                    @if($service->category)
                                        <div class="svc-card__cat-badge">{{ $service->category->name }}</div>
                                    @endif
                                </div>

                                {{-- Body --}}
                                <div class="svc-card__body">
                                    <div class="svc-card__icon-row">
                                        @if($service->icon_class)
                                            <span class="svc-card__icon svc-card__icon--{{ $variant }}">
                                                <i class="{{ $service->icon_class }}"></i>
                                            </span>
                                        @endif
                                        <h5 class="svc-card__title">
                                            {{ $service->getTranslation('name','en') }}
                                        </h5>
                                    </div>

                                    @if($service->short_description)
                                        <p class="svc-card__desc">
                                            {!! \Illuminate\Support\Str::limit(strip_tags($service->short_description), 90) !!}
                                        </p>
                                    @else
                                        <p class="svc-card__desc" style="color:#d1d5db; font-style:italic;">
                                            Professional laundry service
                                        </p>
                                    @endif

                                    <div class="svc-card__footer">
                                        @if($sellPrice)
                                            <div>
                                                <div style="font-size:1.1rem; font-weight:800; color:#0a2463; line-height:1;">
                                                    {{ $currencySymbol }}{{ number_format($sellPrice->price, 2) }}
                                                    <span style="font-size:0.75rem; font-weight:400; color:#9ca3af;">
                                                        {{ $currencyCode }}
                                                    </span>
                                                </div>
                                                <div style="font-size:0.72rem; color:#9ca3af; margin-top:1px;">per order</div>
                                            </div>
                                        @else
                                            <div style="font-size:0.82rem; color:#6c757d;">Price on request</div>
                                        @endif

                                        @if($detailUrl)
                                            <span class="svc-card__cta">
                                                Book Now &#8594;
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($detailUrl) </a> @else </div> @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── CTA Banner ──────────────────────────────────────── --}}
<section class="section-cta-wrap" style="padding-top:60px;">
    <div class="container">
        <div class="cta-section text-center">
            <div class="section-label section-label--light">Limited Time Offer</div>
            <h2 class="cta-title">First Order 20% Off</h2>
            <p class="cta-subtitle">Sign up today and get 20% off your first laundry order. No credit card required.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('customer.register') }}" class="btn btn-light btn-lg fw-semibold px-5 btn-cta-primary">
                    Claim Your Discount
                </a>
                <a href="{{ route('contact') }}" wire:navigate class="btn btn-outline-light btn-lg px-5 btn-cta-outline">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</section>

@include(theme_view('partials.footer'))

@endsection
