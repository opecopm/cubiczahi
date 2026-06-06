@extends('themes.supermarket.layouts.guest')

@section('content')
    <livewire:customer.layout.navigation />

    {{-- ── Page Hero ─────────────────────────────────────────────────── --}}
    <section class="page-hero">
        <div class="container-fluid hero-container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-12">
                    @include('themes.supermarket.partials.breadcrumb', [
                        'variant' => 'light',
                    ])
                    <div class="hero-badge">&#127807;
                        {{ isset($page) && !empty($page->breadcrumb_title) ? $page->breadcrumb_title : __('about.our_story') }}
                    </div>
                    <h1 class="hero-title mt-3 mb-4">{!! isset($page) && !empty($page->title) ? $page->title : 'Your One-Stop <span>Supermarket</span>' !!}</h1>
                    <p class="hero-subtitle mx-auto">
                        {{ isset($page) && !empty($page->subtitle) ? $page->subtitle : __('about.founded_text', ['app_name' => config('app.name')]) }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Optional CMS Content --}}
    @if (isset($page) && !empty($page->content))

        {!! \Illuminate\Support\Facades\Blade::render($page->content) !!}

    @else

        {{-- ── Our Story ─────────────────────────────────────────────────── --}}
        <section class="section-white">
            <div class="container-fluid">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <div class="position-relative"
                            style="min-height: 450px; border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.1);">
                            <!-- Main Image -->
                            <img src="{{ asset('images/supermarket_about_us.png') }}" alt="Supermarket Interior"
                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 24px; position: absolute; inset: 0;">

                            <!-- Floating Card Top Left -->
                            <div class="position-absolute bg-white shadow-lg text-center"
                                style="top: -20px; left: -20px; padding: 1.5rem; border-radius: 16px; width: 160px; z-index: 2; border: 1px solid #e5e7eb;">
                                <div style="font-size: 2.5rem; line-height: 1;">&#128722;</div>
                                <div class="fw-bold mt-2" style="font-size: 0.9rem; color: #1e293b;">10,000+<br><span
                                        class="text-muted fw-normal" style="font-size: 0.8rem;">orders / month</span></div>
                            </div>

                            <!-- Floating Card Bottom Right -->
                            <div class="position-absolute shadow-lg text-center"
                                style="bottom: -20px; right: -20px; padding: 1.5rem; border-radius: 16px; width: 160px; z-index: 2; background: linear-gradient(135deg, #0ea5e9, #38bdf8); color: white; border: 2px solid white;">
                                <div style="font-size: 2.5rem; line-height: 1;">&#11088;</div>
                                <div class="fw-bold mt-2" style="font-size: 0.95rem;">4.8 avg. rating</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="section-label">Our Story</div>
                        <h2 class="section-title mb-4">From a local shop to a global marketplace</h2>
                        <p class="about-text">
                            It started as a small community store in 2020. Our founders realized people wanted a single,
                            reliable place to find everything they need—from the latest tech gadgets to everyday fashion and
                            beauty essentials.
                        </p>
                        <p class="about-text">
                            Today, we offer over 50,000 products, ship to thousands of homes daily, and partner with top
                            brands globally. Whether you are upgrading your home kitchen or your wardrobe, we never
                            compromise on quality or price.
                        </p>
                        <div class="row g-3 mt-2">
                            @foreach ([['2020', 'Founded'], ['50k+', 'Products'], ['100+', 'Top Brands'], ['4.8★', 'Rating']] as $s)
                                <div class="col-6">
                                    <div class="about-stat">
                                        <div class="about-stat__n">{{ $s[0] }}</div>
                                        <div class="about-stat__l">{{ $s[1] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── Mission & Values ──────────────────────────────────────────── --}}
        <section class="section-light">
            <div class="container-fluid">
                <div class="text-center mb-5">
                    <div class="section-label">What Drives Us</div>
                    <h2 class="section-title">Our Mission & Values</h2>
                </div>
                <div class="row g-4">
                    @foreach ([['icon' => '&#127919;', 'color' => 'svc-icon--green', 'title' => 'Our Mission', 'text' => 'To make high-quality products across Electronics, Fashion, and Home & Beauty accessible to everyone — delivering value and convenience to your everyday life.'], ['icon' => '&#127807;', 'color' => 'svc-icon--blue', 'title' => 'Our Vision', 'text' => 'A world where shopping for your daily needs and luxury desires is seamless. We aim to be your most trusted, all-in-one digital supermarket.'], ['icon' => '&#128081;', 'color' => 'svc-icon--amber', 'title' => 'Our Values', 'text' => 'Unbeatable variety. Premium quality. Fast & secure delivery. Customer satisfaction first. These are the principles that drive our supermarket every single day.']] as $v)
                        <div class="col-md-4">
                            <div class="service-card bg-white text-center">
                                <div class="service-icon {{ $v['color'] }} mx-auto">{!! $v['icon'] !!}</div>
                                <h5 class="fw-bold text-dark mb-2">{{ $v['title'] }}</h5>
                                <p class="text-muted card-desc mb-0">{{ $v['text'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ── Team ──────────────────────────────────────────────────────── --}}
        <section class="section-white">
            <div class="container-fluid">
                <div class="text-center mb-5">
                    <div class="section-label">The People</div>
                    <h2 class="section-title">Meet Our Team</h2>
                    <p class="section-subtitle mt-2">Passionate people working hard to bring you the best shopping
                        experience.</p>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach ([['name' => 'Nora Chen', 'role' => 'Founder & CEO', 'color' => 'avatar--emerald', 'i' => 'NC', 'desc' => 'Retail visionary obsessed with curating the best products for our customers.'], ['name' => 'Omar Fahd', 'role' => 'Head of Electronics', 'color' => 'avatar--amber', 'i' => 'OF', 'desc' => 'Tech guru with 15 years of experience sourcing the latest and greatest gadgets.'], ['name' => 'Sana Park', 'role' => 'Fashion & Beauty', 'color' => 'avatar--teal', 'i' => 'SP', 'desc' => 'Trendsetter ensuring our apparel and personal care collections are always on point.'], ['name' => 'Jay Nakamura', 'role' => 'Operations Lead', 'color' => 'avatar--emerald', 'i' => 'JN', 'desc' => 'Logistics mastermind making sure your orders are packed and delivered at lightning speed.']] as $m)
                        <div class="col-sm-6 col-lg-3">
                            <div class="team-card">
                                <div class="team-avatar {{ $m['color'] }}">{{ $m['i'] }}</div>
                                <div class="team-name">{{ $m['name'] }}</div>
                                <div class="team-role">{{ $m['role'] }}</div>
                                <p class="team-desc">{{ $m['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ── CTA ───────────────────────────────────────────────────────── --}}
        <section class="section-cta-wrap">
            <div class="container-fluid">
                <div class="cta-section text-center">
                    <div class="section-label section-label--light">Join the Revolution</div>
                    <h2 class="cta-title">Ready to upgrade your shopping experience?</h2>
                    <p class="cta-subtitle">Explore our vast collections today — from tech to fashion, we have it all.</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ lroute('catalog.index') }}" wire:navigate
                            class="btn btn-light btn-lg fw-semibold px-5 btn-cta-primary">
                            Shop Products
                        </a>
                        <a href="{{ lroute('contact') }}" wire:navigate
                            class="btn btn-outline-light btn-lg px-5 btn-cta-outline">
                            Talk to Us
                        </a>
                    </div>
                </div>
            </div>
        </section>

    @endif

    {{-- ── Footer ───────────────────────────────────────────────────── --}}
    @include(theme_view('partials.footer'))
@endsection
