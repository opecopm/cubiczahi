@extends('themes.supermarket.layouts.guest')

@section('content')

<livewire:customer.layout.navigation />

{{-- ── Header ─────────────────────────────────────────── --}}
<div class="catalog-header">
    <div class="container-fluid position-relative" style="z-index:2; text-align:center;">
        @include('themes.supermarket.partials.breadcrumb', [
            'variant' => 'light',
            'items'   => [['label' => __('nav.shop')]],
        ])
        <div class="catalog-header__count">
            &#10024; {{ $totalCount }} {{ Str::plural('Product', $totalCount) }} Available
        </div>
        <h1 class="hero-title mb-3" style="font-size:clamp(1.9rem,4vw,3rem);">
            Shop Our <span>Entire Collection</span>
        </h1>
        <p class="hero-subtitle mx-auto" style="font-size:1rem; max-width:460px;">
            Everything you need, all in one place.
        </p>
    </div>
</div>

<livewire:customer.catalog.product-list />

@include(theme_view('partials.footer'))

@endsection
