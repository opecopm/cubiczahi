@extends('themes.supermarket.layouts.guest')

@section('content')

<livewire:customer.layout.navigation />

{{-- Page Hero --}}
<div class="page-hero page-hero--sm">
    <div class="container-fluid position-relative" style="z-index:2;">
        @include('themes.supermarket.partials.breadcrumb', [
            'variant' => 'light',
            'items' => [
                ['label' => 'Products', 'url' => lroute('catalog.index')],
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
    <div class="container-fluid">
        <div class="row g-5">

            {{-- Left: Image + Gallery --}}
            <div class="col-lg-6 text-end">
                @if($item->primaryImage)
                    <img src="{{ $item->primaryImage->url }}"
                         alt="{{ $item->getTranslation('name','en') }}"
                         class="w-100 rounded-4 shadow-sm mb-3 ms-auto"
                         style="object-fit:cover; max-height:380px; max-width:400px;">
                @elseif($item->icon_class)
                    <div class="rounded-4 d-flex align-items-center justify-content-center mb-3 ms-auto"
                         style="height:280px; max-width:400px; background:#d1fae5;">
                        <i class="{{ $item->icon_class }}" style="font-size:5rem; color:#059669;"></i>
                    </div>
                @else
                    <div class="rounded-4 d-flex align-items-center justify-content-center mb-3 ms-auto"
                         style="height:280px; max-width:400px; background:#d1fae5; font-size:5rem;">
                        &#129531;
                    </div>
                @endif

                {{-- Additional Images --}}
                @if($item->images && $item->images->count() > 1)
                    <div class="d-flex gap-2 flex-wrap justify-content-end">
                        @foreach($item->images->take(4) as $img)
                            <img src="{{ $img->url }}" alt=""
                                 class="rounded-3 object-fit-cover"
                                 style="width:72px; height:72px; object-fit:cover; cursor:pointer; border:2px solid {{ $img->is_primary ? '#059669' : '#e5e7eb' }};">
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Right: Details --}}
            <div class="col-lg-6">



                {{-- Description --}}
                @if($item->description)
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-2">About This Product</h5>
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
                    <span class="badge rounded-pill px-3 py-2" style="background:#d1fae5;color:#059669;">&#127807; Eco-Friendly</span>
                    <span class="badge rounded-pill px-3 py-2" style="background:#fef3c7;color:#d97706;">&#128666; Fast Shipping</span>
                </div>

                {{-- CTA --}}
                <div class="d-flex gap-3 flex-wrap">
                    <livewire:customer.catalog.add-to-cart :item="$item" />
                </div>

                {{-- Trust row --}}
                <div class="d-flex gap-4 mt-4 pt-4 border-top flex-wrap">
                    @foreach(['&#10003; 30-day returns','&#127807; Eco-friendly','&#128274; Secure checkout'] as $t)
                        <div class="text-muted" style="font-size:0.85rem;">{!! $t !!}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Related Products --}}
@if(isset($related) && $related->isNotEmpty())
<section class="section-white" style="padding-top:70px; padding-bottom:80px;">
    <div class="container-fluid">
        <div class="text-center mb-5">
            <div class="section-label">More Products</div>
            <h2 class="section-title">You Might Also Like</h2>
        </div>
        <div class="row g-4 justify-content-center">
            @php
                $iconVariants = ['green','amber','purple','blue','rose','cyan'];
                $iconEmojis   = ['&#129531;','&#129532;','&#128085;','&#128087;','&#128132;','&#127807;','&#127968;'];
            @endphp
            @foreach($related as $i => $item)
                @php
                    $variant = $iconVariants[$i % count($iconVariants)];
                    $emoji   = $iconEmojis[$i % count($iconEmojis)];
                @endphp
                <div class="col-6 col-md-4 col-xl-3">
                    @include('themes.supermarket.partials.product-card', [
                        'item' => $item,
                        'variant' => $variant,
                        'emoji' => $emoji,
                        'keyPrefix' => 'related',
                        'defaultCurrency' => $defaultCurrency ?? null
                    ])
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@include(theme_view('partials.footer'))

@endsection
