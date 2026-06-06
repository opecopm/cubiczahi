@extends('themes.laundry-one.layouts.guest')

@section('content')

<livewire:customer.layout.navigation />

{{-- Page Hero --}}
<div class="page-hero page-hero--sm">
    <div class="container position-relative" style="z-index:2;">
        @include('themes.laundry-one.partials.breadcrumb', [
            'variant' => 'light',
            'items' => [
                ['label' => 'Services', 'url' => route('catalog.index')],
                ['label' => $item->getTranslation('name', 'en')],
            ],
        ])
        <div class="text-center">
            @if($item->category)
                <div class="hero-badge">{{ $item->category->name }}</div>
            @endif
            <h1 class="hero-title mb-3" style="font-size:clamp(1.8rem,4vw,2.8rem);">
                {{ $item->getTranslation('name','en') }}
            </h1>
            @if($item->short_description)
                <p class="hero-subtitle mx-auto" style="font-size:1rem;">
                    {!! strip_tags($item->short_description) !!}
                </p>
            @endif
        </div>
    </div>
</div>

{{-- Main Content --}}
<section class="section-light" style="padding-top:60px; padding-bottom:80px;">
    <div class="container">
        <div class="row g-5">

            {{-- Left: Image + Gallery --}}
            <div class="col-lg-5">
                @if($item->primaryImage)
                    <img src="{{ $item->primaryImage->url }}"
                         alt="{{ $item->getTranslation('name','en') }}"
                         class="w-100 rounded-4 shadow-sm mb-3"
                         style="object-fit:cover; max-height:380px;">
                @elseif($item->icon_class)
                    <div class="rounded-4 d-flex align-items-center justify-content-center mb-3"
                         style="height:280px; background:#e8f4ff;">
                        <i class="{{ $item->icon_class }}" style="font-size:5rem; color:#0d6efd;"></i>
                    </div>
                @else
                    <div class="rounded-4 d-flex align-items-center justify-content-center mb-3"
                         style="height:280px; background:#e8f4ff; font-size:5rem;">
                        &#128107;
                    </div>
                @endif

                {{-- Additional Images --}}
                @if($item->images && $item->images->count() > 1)
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($item->images->take(4) as $img)
                            <img src="{{ $img->url }}" alt=""
                                 class="rounded-3 object-fit-cover"
                                 style="width:72px; height:72px; object-fit:cover; cursor:pointer; border:2px solid {{ $img->is_primary ? '#0d6efd' : '#e9ecef' }};">
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Right: Details --}}
            <div class="col-lg-7">

                {{-- Price --}}
                @php($sellPrice = $item->prices->where('price_type','sell')->first())
                @if($sellPrice)
                    <div class="mb-4">
                        <div style="font-size:2.2rem; font-weight:800; color:#0a2463; line-height:1;">
                            {{ number_format($sellPrice->price, 2) }}
                            <span style="font-size:1rem; font-weight:400; color:#6c757d;">{{ $sellPrice->currency }}</span>
                        </div>
                        <div class="text-success fw-semibold mt-1" style="font-size:0.85rem;">&#10003; Price includes pickup &amp; delivery</div>
                    </div>
                @else
                    <div class="mb-4">
                        <div class="text-muted fw-semibold">Price on request — contact us for a quote.</div>
                    </div>
                @endif

                {{-- Description --}}
                @if($item->description)
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-2">About This Service</h5>
                        <div class="text-muted" style="font-size:0.96rem; line-height:1.75;">
                            {!! $item->description !!}
                        </div>
                    </div>
                @endif

                {{-- Badges --}}
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @if($item->track_inventory)
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">&#10003; In Stock</span>
                    @endif
                    <span class="badge bg-info-subtle text-info rounded-pill px-3 py-2">&#128197; Book Any Time</span>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">&#128666; Free Pickup</span>
                </div>

                {{-- CTA --}}
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('customer.register') }}" class="btn btn-primary btn-lg fw-semibold px-5"
                       style="border-radius:12px;">
                        Book Now
                    </a>
                    <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary btn-lg px-4"
                       style="border-radius:12px;" wire:navigate>
                        &larr; All Services
                    </a>
                </div>

                {{-- Trust row --}}
                <div class="d-flex gap-4 mt-4 pt-4 border-top flex-wrap">
                    @foreach(['&#10003; Satisfaction guaranteed','&#9889; Fast turnaround','&#128274; Secure & insured'] as $t)
                        <div class="text-muted" style="font-size:0.85rem;">{!! $t !!}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Related Services --}}
@if($related->isNotEmpty())
<section class="section-white" style="padding-top:70px; padding-bottom:80px;">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label">More From Us</div>
            <h2 class="section-title">You Might Also Like</h2>
        </div>
        <div class="row g-4 justify-content-center">
            @php
                $iconColors = ['svc-icon--blue','svc-icon--purple','svc-icon--orange'];
            @endphp
            @foreach($related as $i => $rel)
                @php($relSell = $rel->prices->where('price_type','sell')->first())
                <div class="col-md-6 col-lg-4">
                    <div class="service-card bg-white d-flex flex-column h-100">
                        @if($rel->primaryImage)
                            <img src="{{ $rel->primaryImage->url }}"
                                 alt="{{ $rel->getTranslation('name','en') }}"
                                 class="w-100 rounded-3 mb-3"
                                 style="height:150px; object-fit:cover;">
                        @else
                            <div class="service-icon {{ $iconColors[$i % 3] }} mb-3">
                                @if($rel->icon_class)
                                    <i class="{{ $rel->icon_class }}" style="font-size:1.8rem;"></i>
                                @else
                                    &#128107;
                                @endif
                            </div>
                        @endif
                        <h5 class="fw-bold text-dark mb-2">{{ $rel->getTranslation('name','en') }}</h5>
                        <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                            @if($relSell)
                                <span class="fw-bold text-primary">{{ number_format($relSell->price,2) }} {{ $relSell->currency }}</span>
                            @else
                                <span class="text-muted" style="font-size:0.85rem;">Price on request</span>
                            @endif
                            @if($rel->slug)
                                <a href="{{ route('catalog.show', $rel->slug) }}"
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    View
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@include(theme_view('partials.footer'))

@endsection
