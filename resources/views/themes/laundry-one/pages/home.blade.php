@extends('themes.laundry-one.layouts.guest')

@section('content')

<livewire:customer.layout.navigation />

{{-- ═══════════════════════════════════════ HERO ══════════════════════════════════════ --}}
<section class="hero-section">
    <div class="container hero-container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                @include('themes.laundry-one.partials.breadcrumb', [
                    'variant' => 'light',
                    'items' => [
                        ['label' => 'Home'],
                    ],
                ])
                <div class="hero-badge">&#10024; #1 Laundry Service in Town</div>
                <h1 class="hero-title mb-4">
                    Fresh Clothes,<br>
                    <span>Delivered</span> to<br>
                    Your Door
                </h1>
                <p class="hero-subtitle mb-4">
                    Skip the laundromat. We pick up your clothes, clean them professionally, and deliver them back — fresh, folded, and on time.
                </p>
                <div class="d-flex gap-3 flex-wrap mb-2">
                    <a href="{{ route('customer.register') }}" class="btn btn-light btn-lg fw-semibold px-4 btn-hero-primary">
                        Get Started Free
                    </a>
                    <a href="#how-it-works" class="btn btn-outline-light btn-lg px-4 btn-hero-outline">
                        How It Works
                    </a>
                </div>
                <div class="trust-badges">
                    <div class="trust-badge"><div class="check">&#10003;</div> Free Pickup</div>
                    <div class="trust-badge"><div class="check">&#10003;</div> 24h Delivery</div>
                    <div class="trust-badge"><div class="check">&#10003;</div> 100% Satisfaction</div>
                </div>
            </div>

            <div class="col-lg-5 offset-lg-1">
                <div class="hero-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="fw-bold text-dark hero-card-title">Your Orders</div>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">Live Tracking</span>
                    </div>
                    @foreach([
                        ['icon'=>'&#128107;','bg'=>'order-icon--blue',  'label'=>'Wash & Fold — 3kg', 'status'=>'Delivered',  'sc'=>'success'],
                        ['icon'=>'&#128176;','bg'=>'order-icon--orange','label'=>'Dry Clean — Suit',  'status'=>'In Progress','sc'=>'warning'],
                        ['icon'=>'&#9889;',  'bg'=>'order-icon--green', 'label'=>'Express — Shirts x4','status'=>'Picked Up', 'sc'=>'info'],
                    ] as $order)
                    <div class="order-row">
                        <div class="order-icon {{ $order['bg'] }}">{!! $order['icon'] !!}</div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold text-dark order-label">{{ $order['label'] }}</div>
                        </div>
                        <span class="status-pill bg-{{ $order['sc'] }}-subtle text-{{ $order['sc'] }}">
                            {{ $order['status'] }}
                        </span>
                    </div>
                    @endforeach
                    <div class="mt-3 p-3 rounded-3 text-center order-next">
                        <div class="text-muted order-next-label">Next delivery in</div>
                        <div class="fw-bold text-primary fs-5">2h 45m</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════ SERVICES ════════════════════════════════════ --}}
<section id="services" class="section-light">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label">What We Offer</div>
            <h2 class="section-title">Our Laundry Services</h2>
            <p class="section-subtitle mt-2">
                From everyday washing to delicate dry cleaning — we handle every fabric with care.
            </p>
        </div>

        <div class="row g-4">
            @php
                $iconColors = ['svc-icon--blue','svc-icon--purple','svc-icon--orange','svc-icon--green','svc-icon--red','svc-icon--cyan'];
                $fallbackServices = [
                    ['icon'=>'&#128107;','bg'=>'svc-icon--blue',  'title'=>'Wash & Fold',
                     'desc'=>'We wash, dry, and fold your everyday clothes with premium detergents. Ready in 24 hours.'],
                    ['icon'=>'&#129309;','bg'=>'svc-icon--purple','title'=>'Dry Cleaning',
                     'desc'=>'Professional dry cleaning for suits, dresses, and delicate fabrics. Stain-free guaranteed.'],
                    ['icon'=>'&#129455;','bg'=>'svc-icon--orange','title'=>'Ironing & Pressing',
                     'desc'=>'Crisp, wrinkle-free clothes returned neatly hung or folded — ready to wear.'],
                    ['icon'=>'&#128336;','bg'=>'svc-icon--green', 'title'=>'Express Service',
                     'desc'=>'Need it fast? Our express service delivers same-day for urgent orders.'],
                    ['icon'=>'&#128098;','bg'=>'svc-icon--red',   'title'=>'Shoe Cleaning',
                     'desc'=>'Restore your sneakers and shoes to like-new condition with our specialist cleaning.'],
                    ['icon'=>'&#128717;','bg'=>'svc-icon--cyan',  'title'=>'Pickup & Delivery',
                     'desc'=>'Schedule a free pickup at your door. We deliver back clean within your chosen timeframe.'],
                ];
            @endphp

            @if(isset($featuredServices) && $featuredServices->isNotEmpty())
                @foreach($featuredServices as $i => $service)
                    @php
                        $sellPrice      = $service->prices->where('price_type','sell')->first();
                        $currencyCode   = $defaultCurrency?->code ?? $sellPrice?->currency ?? '';
                        $currencySymbol = $defaultCurrency?->symbol_left ?? $defaultCurrency?->symbol_right ?? '';
                        $colorClass     = $iconColors[$i % count($iconColors)];
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="service-card bg-white d-flex flex-column h-100">
                            <div class="service-icon {{ $colorClass }}">
                                @if($service->icon_class)
                                    <i class="{{ $service->icon_class }}" style="font-size:1.8rem;"></i>
                                @else
                                    &#128107;
                                @endif
                            </div>
                            <h5 class="fw-bold text-dark mb-2">{{ $service->getTranslation('name','en') }}</h5>
                            <p class="text-muted card-desc mb-3 flex-grow-1">
                                {!! \Illuminate\Support\Str::limit(strip_tags($service->short_description ?? $service->description ?? ''), 100) !!}
                            </p>
                            <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                                @if($sellPrice)
                                    <span class="fw-bold text-primary">
                                        {{ $currencySymbol }}{{ number_format($sellPrice->price,2) }} {{ $currencyCode }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:0.82rem;">Price on request</span>
                                @endif
                                @if($service->slug)
                                    <a href="{{ route('catalog.show', $service->slug) }}"
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3" wire:navigate>
                                        Learn More
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                @foreach($fallbackServices as $svc)
                <div class="col-md-6 col-lg-4">
                    <div class="service-card bg-white">
                        <div class="service-icon {{ $svc['bg'] }}">{!! $svc['icon'] !!}</div>
                        <h5 class="fw-bold text-dark mb-2">{{ $svc['title'] }}</h5>
                        <p class="text-muted card-desc mb-0">{{ $svc['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        @if(isset($featuredServices) && $featuredServices->isNotEmpty())
        <div class="text-center mt-5">
            <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-lg px-5 fw-semibold"
               style="border-radius:12px;" wire:navigate>
                View All Services &rarr;
            </a>
        </div>
        @endif
    </div>
</section>

{{-- ═════════════════════════════════════ HOW IT WORKS ════════════════════════════════ --}}
<section id="how-it-works" class="section-white">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label">Simple Process</div>
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle mt-2">
                Getting clean laundry has never been easier. Just 3 simple steps.
            </p>
        </div>

        <div class="row g-4 text-center">
            @foreach([
                ['n'=>'1','icon'=>'&#128197;','title'=>'Schedule Pickup',
                 'desc'=>'Choose your preferred pickup time through our app or website. We come to you.'],
                ['n'=>'2','icon'=>'&#128107;','title'=>'We Clean It',
                 'desc'=>'Our professional team cleans, dries, and folds your laundry using premium products.'],
                ['n'=>'3','icon'=>'&#128666;','title'=>'Fresh Delivery',
                 'desc'=>'We deliver your fresh, clean clothes back to your door at your chosen time.'],
            ] as $step)
            <div class="col-md-4 position-relative">
                <div class="step-number">{{ $step['n'] }}</div>
                <div class="step-emoji">{!! $step['icon'] !!}</div>
                <h5 class="fw-bold text-dark mb-2">{{ $step['title'] }}</h5>
                <p class="text-muted mx-auto step-desc">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════ STATS ══════════════════════════════════════ --}}
<section class="stats-section">
    <div class="container">
        <div class="row g-4 align-items-center justify-content-center text-center">
            @foreach([
                ['n'=>'15,000+','label'=>'Happy Customers'],
                ['n'=>'50,000+','label'=>'Orders Completed'],
                ['n'=>'4.9 ★', 'label'=>'Average Rating'],
                ['n'=>'25+',   'label'=>'Cities Covered'],
            ] as $i => $stat)
                @if($i > 0)
                    <div class="col-auto d-none d-md-block"><div class="stat-divider"></div></div>
                @endif
                <div class="col-6 col-md-auto px-md-4">
                    <div class="stat-number">{{ $stat['n'] }}</div>
                    <div class="stat-label mt-1">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════ PRICING ════════════════════════════════════ --}}
<section id="pricing" class="section-light">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label">Simple Pricing</div>
            <h2 class="section-title">Choose Your Plan</h2>
            <p class="section-subtitle mt-2">
                No hidden fees. No surprises. Just clean clothes at a fair price.
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach([
                ['name'=>'Basic',   'price'=>'9', 'period'=>'/ order','desc'=>'Perfect for occasional laundry needs',
                 'features'=>[[true,'Up to 5kg per order'],[true,'Wash & Fold only'],[true,'48h delivery'],
                              [true,'Free pickup'],[false,'Dry cleaning'],[false,'Priority support']],
                 'btn'=>'btn-outline-primary','featured'=>false],
                ['name'=>'Standard','price'=>'24','period'=>'/ month','desc'=>'Best value for regular customers',
                 'features'=>[[true,'Up to 15kg per month'],[true,'Wash, Fold & Iron'],[true,'24h delivery'],
                              [true,'Free pickup'],[true,'2 dry clean items'],[false,'Priority support']],
                 'btn'=>'btn-primary','featured'=>true],
                ['name'=>'Premium', 'price'=>'49','period'=>'/ month','desc'=>'For families and power users',
                 'features'=>[[true,'Unlimited weight'],[true,'All services included'],[true,'Same-day delivery'],
                              [true,'Free pickup'],[true,'Unlimited dry cleaning'],[true,'Priority support']],
                 'btn'=>'btn-outline-primary','featured'=>false],
            ] as $plan)
            <div class="col-md-6 col-lg-4">
                <div class="pricing-card {{ $plan['featured'] ? 'featured' : 'bg-white' }}">
                    @if($plan['featured'])
                        <div class="text-center mb-3">
                            <span class="badge bg-primary rounded-pill px-3 py-2 badge-popular">
                                &#11088; Most Popular
                            </span>
                        </div>
                    @endif
                    <div class="fw-bold text-dark mb-1 plan-name">{{ $plan['name'] }}</div>
                    <div class="text-muted mb-3 plan-desc">{{ $plan['desc'] }}</div>
                    <div class="price mb-4">
                        <sup>$</sup>{{ $plan['price'] }}<span>{{ $plan['period'] }}</span>
                    </div>
                    <div class="mb-4">
                        @foreach($plan['features'] as [$on, $text])
                        <div class="feature-item">
                            @if($on)
                                <span class="check">&#10003;</span>
                                <span>{{ $text }}</span>
                            @else
                                <span class="cross">&#10007;</span>
                                <span class="feature-text--off">{{ $text }}</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('customer.register') }}" class="btn {{ $plan['btn'] }} w-100 fw-semibold btn-plan">
                        Get Started
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═════════════════════════════════════ TESTIMONIALS ════════════════════════════════ --}}
<section class="section-white">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label">Happy Customers</div>
            <h2 class="section-title">What People Say</h2>
        </div>

        <div class="row g-4">
            @foreach([
                ['name'=>'Sarah M.','role'=>'Working Mom',       'avatar'=>'avatar--purple','initial'=>'S',
                 'text'=>'"Absolutely love this service! I used to spend my entire Sunday doing laundry. Now I schedule a pickup and everything comes back perfectly folded. Game changer!"'],
                ['name'=>'James R.','role'=>'Business Executive','avatar'=>'avatar--blue',  'initial'=>'J',
                 'text'=>'"The dry cleaning for my suits is impeccable. They handle delicate fabrics better than any service I\'ve tried. Reliable, professional, and always on time."'],
                ['name'=>'Priya K.','role'=>'Student',           'avatar'=>'avatar--green', 'initial'=>'P',
                 'text'=>'"As a student, the Basic plan is perfect for me. Affordable, fast, and my clothes always smell amazing. The app makes scheduling super easy too!"'],
            ] as $t)
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="stars mb-3">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="text-muted review-text mb-4">{{ $t['text'] }}</p>
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle {{ $t['avatar'] }}">{{ $t['initial'] }}</div>
                        <div>
                            <div class="fw-bold text-dark reviewer-name">{{ $t['name'] }}</div>
                            <div class="text-muted reviewer-role">{{ $t['role'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════ CTA ═══════════════════════════════════════ --}}
<section class="section-cta-wrap">
    <div class="container">
        <div class="cta-section text-center">
            <div class="section-label section-label--light">Limited Time Offer</div>
            <h2 class="cta-title">First Order 20% Off</h2>
            <p class="cta-subtitle">
                Sign up today and get 20% off your first laundry order. No credit card required.
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('customer.register') }}" class="btn btn-light btn-lg fw-semibold px-5 btn-cta-primary">
                    Claim Your Discount
                </a>
                <a href="{{ route('customer.login') }}" class="btn btn-outline-light btn-lg px-5 btn-cta-outline">
                    Sign In
                </a>
            </div>
        </div>
    </div>
</section>

@include(theme_view('partials.footer'))

@endsection
