@extends('themes.supermarket.layouts.guest')

@section('content')
<livewire:customer.layout.navigation />

<div class="catalog-header">
    <div class="container-fluid position-relative" style="z-index:2; text-align:center;">
        @include('themes.supermarket.partials.breadcrumb', [
            'variant' => 'light',
            'items' => [
                ['label' => 'Privacy Policy'],
            ],
        ])
        <h1 class="hero-title mb-3 text-white" style="font-size:clamp(1.9rem,4vw,3rem);">
            Privacy Policy
        </h1>
        <p class="hero-subtitle mx-auto text-white-50" style="font-size:1rem; max-width:460px;">
            Last Updated: {{ date('F d, Y') }}
        </p>
    </div>
</div>

<div class="container py-5" style="max-width: 800px;">

    <div class="legal-content mt-4" style="line-height: 1.8;">
        <h4>1. Information We Collect</h4>
        <p>We collect information that you provide directly to us, such as when you create or modify your account, request on-demand services, contact customer support, or otherwise communicate with us.</p>

        <h4>2. How We Use Your Information</h4>
        <p>We may use the information we collect about you to provide, maintain, and improve our services, including to facilitate payments, send receipts, provide products and services you request, and send related information.</p>

        <h4>3. Data Security</h4>
        <p>We take reasonable measures to help protect information about you from loss, theft, misuse and unauthorized access, disclosure, alteration and destruction.</p>

        <h4>4. Contact Us</h4>
        <p>If you have any questions about this Privacy Policy, please contact us:</p>
        <ul class="list-unstyled mt-3">
            <li class="mb-2"><strong>&#128222; Phone:</strong> +1 (800) DETERGENT</li>
            <li><strong>&#9993; Email:</strong> hello@detergentshop.com</li>
        </ul>
    </div>
</div>

@include(theme_view('partials.footer'))
@endsection
