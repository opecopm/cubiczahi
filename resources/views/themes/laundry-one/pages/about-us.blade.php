@extends('themes.laundry-one.layouts.guest')

@section('content')

<livewire:customer.layout.navigation />

{{-- ── Page Hero ─────────────────────────────────────────────────── --}}
<section class="page-hero">
    <div class="container hero-container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-7">
                @include('themes.laundry-one.partials.breadcrumb', [
                    'variant' => 'light',
                ])
                <div class="hero-badge">&#127968; Our Story</div>
                <h1 class="hero-title mt-3 mb-4">We Make Laundry Day<br><span>Disappear</span></h1>
                <p class="hero-subtitle mx-auto">
                    Founded in 2019, {{ config('app.name') }} set out with one mission: give people their time back by making laundry effortless, affordable, and reliable.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ── Our Story ─────────────────────────────────────────────────── --}}
<section class="section-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="about-img-wrap">
                    <div class="about-img-card about-img-card--main">
                        <div class="about-img-icon">&#128107;</div>
                        <div class="about-img-label">10,000+ orders / month</div>
                    </div>
                    <div class="about-img-card about-img-card--float">
                        <div class="about-img-icon">&#11088;</div>
                        <div class="about-img-label">4.9 avg. rating</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-label">Our Story</div>
                <h2 class="section-title mb-4">Started from a single washing machine</h2>
                <p class="about-text">
                    It started in a small apartment in 2019. Our founder, tired of spending every Sunday at the laundromat, decided there had to be a better way. With one washing machine and a simple website, the first order came in within days.
                </p>
                <p class="about-text">
                    Today we operate across 25+ cities, employ over 200 professionals, and process thousands of orders every day — all while staying true to our original promise: clean clothes, delivered to your door, on time.
                </p>
                <div class="row g-3 mt-2">
                    @foreach([['2019','Founded'],['25+','Cities'],['200+','Team Members'],['4.9★','Rating']] as $s)
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
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label">What Drives Us</div>
            <h2 class="section-title">Our Mission & Values</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['icon'=>'&#127919;','color'=>'svc-icon--blue',  'title'=>'Our Mission',
                 'text'=>'To make professional-quality laundry accessible to everyone — saving time, reducing stress, and delivering consistency you can count on.'],
                ['icon'=>'&#128140;','color'=>'svc-icon--green', 'title'=>'Our Vision',
                 'text'=>'A world where nobody wastes their weekend doing laundry. We want to be the most trusted laundry brand in every city we operate.'],
                ['icon'=>'&#128081;','color'=>'svc-icon--purple','title'=>'Our Values',
                 'text'=>'Honesty in pricing. Reliability in delivery. Care for every garment. Respect for your time. These are the principles we refuse to compromise on.'],
            ] as $v)
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
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label">The People</div>
            <h2 class="section-title">Meet Our Team</h2>
            <p class="section-subtitle mt-2">Passionate people working hard so your clothes come back perfect.</p>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach([
                ['name'=>'Alex Johnson','role'=>'Founder & CEO',   'color'=>'avatar--blue',  'i'=>'AJ','desc'=>'Ex-logistics engineer who got tired of doing laundry on Sundays.'],
                ['name'=>'Maria Santos','role'=>'Head of Operations','color'=>'avatar--purple','i'=>'MS','desc'=>'Keeps 200+ staff and 25 cities running like clockwork every day.'],
                ['name'=>'David Kim',  'role'=>'Tech Lead',         'color'=>'avatar--green', 'i'=>'DK','desc'=>'Built the platform from scratch. Obsessed with on-time delivery metrics.'],
                ['name'=>'Sara Ahmed', 'role'=>'Head of Quality',   'color'=>'avatar--blue',  'i'=>'SA','desc'=>'Every garment passes her quality standard before it leaves our hands.'],
            ] as $m)
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
    <div class="container">
        <div class="cta-section text-center">
            <div class="section-label section-label--light">Join the Family</div>
            <h2 class="cta-title">Ready to reclaim your weekends?</h2>
            <p class="cta-subtitle">Schedule your first pickup today — no commitment, no hidden fees.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('customer.register') }}" class="btn btn-light btn-lg fw-semibold px-5 btn-cta-primary">
                    Get Started Free
                </a>
                <a href="{{ route('contact') }}" wire:navigate class="btn btn-outline-light btn-lg px-5 btn-cta-outline">
                    Talk to Us
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ── Footer ───────────────────────────────────────────────────── --}}
@include(theme_view('partials.footer'))

@endsection
