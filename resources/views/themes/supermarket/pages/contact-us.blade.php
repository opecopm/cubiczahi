@extends('themes.supermarket.layouts.guest')

@section('content')
    <livewire:customer.layout.navigation />

    {{-- ── Page Hero ─────────────────────────────────────────────────── --}}
    <section class="page-hero page-hero--sm">
        <div class="container-fluid hero-container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-12">
                    @include('themes.supermarket.partials.breadcrumb', [
                        'variant' => 'light',
                    ])
                    <div class="hero-badge">&#128222; {{ __('contact.hero_badge') }}</div>
                    <h1 class="hero-title mt-3 mb-4">{!! __('contact.hero_title') !!}</h1>
                    <p class="hero-subtitle mx-auto">
                        {{ __('contact.hero_subtitle') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Optional CMS Content --}}
    @if (isset($page) && !empty($page->content))

        {!! \Illuminate\Support\Facades\Blade::render($page->content) !!}
    @else
        {{-- ── Info Cards ───────────────────────────────────────────────── --}}
        <section class="section-light">
            <div class="container-fluid">
                <div class="row g-4 justify-content-center">
                    @foreach ([['icon' => '&#128222;', 'bg' => 'svc-icon--green', 'title' => 'Call Us', 'lines' => ['+1 (800) DETERGENT', 'Mon–Sat, 8am–8pm']], ['icon' => '&#9993;', 'bg' => 'svc-icon--amber', 'title' => 'Email Us', 'lines' => ['hello@detergentshop.com', 'We reply within 2 hours']], ['icon' => '&#128205;', 'bg' => 'svc-icon--purple', 'title' => 'Visit Us', 'lines' => ['456 Clean Avenue, Suite 12', 'Los Angeles, CA 90001']], ['icon' => '&#128336;', 'bg' => 'svc-icon--blue', 'title' => 'Working Hours', 'lines' => ['Mon–Fri: 7am – 9pm', 'Sat–Sun: 9am – 5pm']]] as $card)
                        <div class="col-sm-6 col-lg-3">
                            <div class="contact-info-card">
                                <div class="service-icon {{ $card['bg'] }} mb-3">{!! $card['icon'] !!}</div>
                                <div class="contact-info-title">{{ $card['title'] }}</div>
                                @foreach ($card['lines'] as $line)
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
            <div class="container-fluid">
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
                                        <option>Product Question</option>
                                        <option>Wholesale Inquiry</option>
                                        <option>Returns & Refunds</option>
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
                            @foreach ([['q' => 'How long does shipping take?', 'a' => 'Standard delivery is 2-3 business days. Express shipping delivers next-day for orders placed before 2pm.'], ['q' => 'Do you offer refills?', 'a' => 'Yes! Our refill pouches use 80% less plastic. Available for all liquid detergent and softener products.'], ['q' => 'Are your products eco-friendly?', 'a' => 'All our products use plant-based ingredients, biodegradable formulas, and recyclable or refillable packaging.'], ['q' => 'What if I\'m not satisfied?', 'a' => 'We offer a 30-day satisfaction guarantee. Return any product for a full refund — no questions asked.']] as $faq)
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

    @endif

    {{-- ── Footer ───────────────────────────────────────────────────── --}}
    @include(theme_view('partials.footer'))
@endsection

