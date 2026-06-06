@extends('themes.supermarket.layouts.guest')

@section('content')
<livewire:customer.layout.navigation />

<div class="catalog-header">
    <div class="container-fluid position-relative" style="z-index:2; text-align:center;">
        @include('themes.supermarket.partials.breadcrumb', [
            'variant' => 'light',
            'items' => [
                ['label' => 'Refund Policy'],
            ],
        ])
        <h1 class="hero-title mb-3 text-white" style="font-size:clamp(1.9rem,4vw,3rem);">
            Refund Policy
        </h1>
        <p class="hero-subtitle mx-auto text-white-50" style="font-size:1rem; max-width:460px;">
            Last Updated: {{ date('F d, Y') }}
        </p>
    </div>
</div>

<div class="container py-5" style="max-width: 800px;">

    <div class="legal-content mt-4" style="line-height: 1.8;">
        <h4>1. Return Window</h4>
        <p>We have a 30-day return policy, which means you have 30 days after receiving your item to request a return. To be eligible for a return, your item must be in the same condition that you received it, unworn or unused, with tags, and in its original packaging.</p>

        <h4>2. Process for Returns</h4>
        <p>To start a return, you can contact us at hello@detergentshop.com. If your return is accepted, we’ll send you a return shipping label, as well as instructions on how and where to send your package.</p>

        <h4>3. Damages and issues</h4>
        <p>Please inspect your order upon reception and contact us immediately if the item is defective, damaged or if you receive the wrong item, so that we can evaluate the issue and make it right.</p>

        <h4>4. Refunds</h4>
        <p>We will notify you once we’ve received and inspected your return, and let you know if the refund was approved or not. If approved, you’ll be automatically refunded on your original payment method.</p>

        <h4>5. Contact Us</h4>
        <p>For refunds or questions regarding your return, please contact us:</p>
        <ul class="list-unstyled mt-3">
            <li class="mb-2"><strong>&#128222; Phone:</strong> +1 (800) DETERGENT</li>
            <li><strong>&#9993; Email:</strong> hello@detergentshop.com</li>
        </ul>
    </div>
</div>

@include(theme_view('partials.footer'))
@endsection
