@extends('themes.supermarket.layouts.guest')

@section('content')

<livewire:customer.layout.navigation />

{{-- ── Header ─────────────────────────────────────────── --}}
<div class="catalog-header" style="background: linear-gradient(135deg, #ef4444, #b91c1c);">
    <div class="container-fluid position-relative" style="z-index:2; text-align:center;">
        @include('themes.supermarket.partials.breadcrumb', [
            'variant' => 'light',
            'items' => [
                ['label' => 'Deals'],
            ],
        ])
        <h1 class="hero-title mb-3 text-white" style="font-size:clamp(1.9rem,4vw,3rem);">
            <span style="color: #fee2e2;">Hot Deals</span> & Discounts
        </h1>
        <p class="hero-subtitle mx-auto text-white-50" style="font-size:1rem; max-width:460px;">
            Shop the best offers of the week across all categories. Don't miss out!
        </p>
    </div>
</div>

<livewire:customer.catalog.product-list />

@include(theme_view('partials.footer'))

@endsection
