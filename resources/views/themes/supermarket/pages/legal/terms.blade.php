@extends('themes.supermarket.layouts.guest')

@section('content')
<livewire:customer.layout.navigation />

<div class="catalog-header">
    <div class="container-fluid position-relative" style="z-index:2; text-align:center;">
        @include('themes.supermarket.partials.breadcrumb', [
            'variant' => 'light',
            'items' => [
                ['label' => 'Terms and Conditions'],
            ],
        ])
        <h1 class="hero-title mb-3 text-white" style="font-size:clamp(1.9rem,4vw,3rem);">
            Terms and Conditions
        </h1>
        <p class="hero-subtitle mx-auto text-white-50" style="font-size:1rem; max-width:460px;">
            Last Updated: {{ date('F d, Y') }}
        </p>
    </div>
</div>

<div class="container py-5" style="max-width: 800px;">

    <div class="legal-content mt-4" style="line-height: 1.8;">
        <h4>1. Agreement to Terms</h4>
        <p>By accessing our website, you agree to be bound by these Terms of Service and to use the site in accordance with these Terms of Service, our Privacy Policy and any additional terms and conditions that may apply to specific sections of the site.</p>

        <h4>2. Products and Services</h4>
        <p>All descriptions of products or product pricing are subject to change at anytime without notice, at the sole discretion of us. We reserve the right to discontinue any product at any time.</p>

        <h4>3. User Accounts</h4>
        <p>If you create an account on the Website, you are responsible for maintaining the security of your account, and you are fully responsible for all activities that occur under the account.</p>

        <h4>4. Contact</h4>
        <p>Questions about the Terms of Service should be sent to us at:</p>
        <ul class="list-unstyled mt-3">
            <li class="mb-2"><strong>&#128222; Phone:</strong> +1 (800) DETERGENT</li>
            <li><strong>&#9993; Email:</strong> hello@detergentshop.com</li>
        </ul>
    </div>
</div>

@include(theme_view('partials.footer'))
@endsection
