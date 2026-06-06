@extends(theme_view('layouts.guest'))

@section('content')
    <livewire:customer.layout.navigation />

    <div class="page-hero page-hero--sm">
        <div class="container-fluid position-relative" style="z-index:2;">
            <div class="text-center">
                <h1 class="hero-title mb-3" style="font-size:clamp(1.8rem,4vw,2.8rem);">Secure Checkout</h1>
            </div>
        </div>
    </div>

    <section class="section-light" style="padding-top:60px; padding-bottom:80px; min-height:50vh;">
        <div class="container-fluid">
            <livewire:customer.shop.checkout-page />
        </div>
    </section>

    @include(theme_view('partials.footer'))
@endsection
