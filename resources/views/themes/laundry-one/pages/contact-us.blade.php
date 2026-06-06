@extends('themes.laundry-one.layouts.guest')

@section('content')

<livewire:customer.layout.navigation />

{{-- ── Page Hero ─────────────────────────────────────────────────── --}}
<section class="page-hero page-hero--sm">
    <div class="container hero-container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-6">
                @include('themes.laundry-one.partials.breadcrumb', [
                    'variant' => 'light',
                ])
                <div class="hero-badge">&#128222; Get in Touch</div>
                <h1 class="hero-title mt-3 mb-4">We'd Love to<br><span>Hear From You</span></h1>
                <p class="hero-subtitle mx-auto">
                    Have a question, feedback, or want to partner with us? Our team is here and happy to help.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ── Info Cards ───────────────────────────────────────────────── --}}
<section class="section-light">
    <div class="container">
        <div class="row g-4 justify-content-center">
            @foreach([
                ['icon'=>'&#128222;','bg'=>'svc-icon--blue',  'title'=>'Call Us',
                 'lines'=>['+1 (800) LAUNDRY','Mon–Sat, 8am–8pm']],
                ['icon'=>'&#9993;',  'bg'=>'svc-icon--purple','title'=>'Email Us',
                 'lines'=>['hello@openlaundry.com','We reply within 2 hours']],
                ['icon'=>'&#128205;','bg'=>'svc-icon--orange','title'=>'Visit Us',
                 'lines'=>['123 Clean Street, Suite 4','New York, NY 10001']],
                ['icon'=>'&#128336;','bg'=>'svc-icon--green', 'title'=>'Working Hours',
                 'lines'=>['Mon–Fri: 7am – 9pm','Sat–Sun: 8am – 6pm']],
            ] as $card)
            <div class="col-sm-6 col-lg-3">
                <div class="contact-info-card">
                    <div class="service-icon {{ $card['bg'] }} mb-3">{!! $card['icon'] !!}</div>
                    <div class="contact-info-title">{{ $card['title'] }}</div>
                    @foreach($card['lines'] as $line)
                        <div class="contact-info-line">{{ $line }}</div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Contact Form ──────────────────────────────────────────────── --}}
<section class="section-white">
    <div class="container">
        <div class="row g-5 align-items-start justify-content-center">

            <div class="col-lg-7">
                <div class="section-label">Send a Message</div>
                <h2 class="section-title mb-4">We typically reply<br>within 2 hours</h2>

                <form class="contact-form">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="auth-label">Full Name</label>
                            <input type="text" class="auth-input" placeholder="John Doe" />
                        </div>
                        <div class="col-md-6">
                            <label class="auth-label">Email Address</label>
                            <input type="email" class="auth-input" placeholder="your@email.com" />
                        </div>
                        <div class="col-12">
                            <label class="auth-label">Phone (optional)</label>
                            <input type="tel" class="auth-input" placeholder="+1 (555) 000-0000" />
                        </div>
                        <div class="col-12">
                            <label class="auth-label">Subject</label>
                            <select class="auth-input">
                                <option value="">Select a topic…</option>
                                <option>Order Issue</option>
                                <option>Billing Question</option>
                                <option>Service Inquiry</option>
                                <option>Partnership</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="auth-label">Message</label>
                            <textarea class="auth-input" rows="5" placeholder="Tell us how we can help…"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-auth-submit" style="max-width:240px;">
                                Send Message &#8594;
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-4 offset-lg-1">
                <div class="contact-faq">
                    <div class="section-label">FAQ</div>
                    <h3 class="section-title mb-4" style="font-size:1.4rem;">Common Questions</h3>
                    @foreach([
                        ['q'=>'How do I schedule a pickup?',   'a'=>"Create a free account, choose your service, and pick a time slot. We'll be at your door."],
                        ['q'=>'What areas do you serve?',      'a'=>'We currently operate in 25+ cities. Enter your zip code on the homepage to check availability.'],
                        ['q'=>'How long does it take?',        'a'=>'Standard service is 24–48 hours. Express orders are returned same-day before 6pm.'],
                        ['q'=>"What if I'm not satisfied?",    'a'=>"We offer a 100% satisfaction guarantee. We'll re-clean for free or give you a full refund."],
                    ] as $faq)
                    <div class="faq-item">
                        <div class="faq-q">{{ $faq['q'] }}</div>
                        <div class="faq-a">{{ $faq['a'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── Footer ───────────────────────────────────────────────────── --}}
@include(theme_view('partials.footer'))

@endsection
