@php
    $footerServices = \Modules\Inventory\Models\Item::where('status','active')
        ->where('type','service')
        ->select('id','name','slug')
        ->orderBy('name->'.app()->getLocale())
        ->take(6)
        ->get();

    // Fetch dynamic Web Settings
    $siteName     = web_setting('site_name', config('app.name', 'LaundryPro'));
    $footerAbout  = web_setting('footer_about', 'Professional laundry and dry cleaning delivered to your door. Trusted by thousands across the country.');
    $footerText   = web_setting('footer_text', 'All rights reserved.');
    
    // Logos
    $logoModel    = \Modules\CMS\Models\WebSetting::where('key', 'footer_logo')->first();
    $footerLogoUrl = $logoModel ? $logoModel->getFirstMediaUrl('footer_logo') : null;

    // Contact
    $contactEmail   = web_setting('contact_email', 'hello@openlaundry.com');
    $contactPhone   = web_setting('contact_phone', '+1 (800) LAUNDRY');
    $contactAddress = web_setting('contact_address');

    // Social Links
    $socials = [
        'social_facebook'  => ['label' => 'F',  'url' => web_setting('social_facebook')],
        'social_twitter'   => ['label' => 'T',  'url' => web_setting('social_twitter')],
        'social_instagram' => ['label' => 'in', 'url' => web_setting('social_instagram')],
        'social_linkedin'  => ['label' => 'YT', 'url' => web_setting('social_linkedin')],
        'social_whatsapp'  => ['label' => 'WA', 'url' => web_setting('social_whatsapp')],
    ];

    // Fallback social defaults if all are empty
    $hasAnySocial = collect($socials)->contains(fn($s) => !empty($s['url']));
    if (!$hasAnySocial) {
        $socials['social_facebook']['url'] = '#';
        $socials['social_twitter']['url'] = '#';
        $socials['social_instagram']['url'] = '#';
        $socials['social_linkedin']['url'] = '#';
    }
@endphp

<footer class="site-footer">
    <div class="container">
        <div class="row g-5">
            {{-- Brand --}}
            <div class="col-lg-4">
                <div class="footer-brand mb-3">
                    @if($footerLogoUrl)
                        <img src="{{ $footerLogoUrl }}" alt="{{ $siteName }}" style="max-height: 48px; object-fit: contain;">
                    @else
                        &#9910; {{ $siteName }}
                    @endif
                </div>
                <p class="footer-tagline">
                    {{ $footerAbout }}
                </p>
                <div class="d-flex gap-3 mt-4">
                    @foreach($socials as $key => $social)
                        @if(!empty($social['url']))
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="d-flex align-items-center justify-content-center rounded-circle social-btn" title="{{ ucfirst(explode('_', $key)[1]) }}">
                                {{ $social['label'] }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Services --}}
            <div class="col-6 col-lg-2">
                <div class="footer-heading">Services</div>
                <ul class="footer-links">
                    @forelse($footerServices as $svc)
                        <li>
                            @if($svc->slug)
                                <a href="{{ route('catalog.show', $svc->slug) }}" wire:navigate>
                                    {{ $svc->getTranslation('name', app()->getLocale()) }}
                                </a>
                            @else
                                <a href="{{ route('catalog.index') }}" wire:navigate>
                                    {{ $svc->getTranslation('name', app()->getLocale()) }}
                                </a>
                            @endif
                        </li>
                    @empty
                        <li><a href="{{ route('catalog.index') }}" wire:navigate>View All Services</a></li>
                    @endforelse
                    @if($footerServices->isNotEmpty())
                        <li>
                            <a href="{{ route('catalog.index') }}" wire:navigate
                               style="color:#7dd3fc; font-weight:600;">
                                View All &rarr;
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Company --}}
            <div class="col-6 col-lg-2">
                <div class="footer-heading">Company</div>
                <ul class="footer-links">
                    <li><a href="{{ route('about') }}" wire:navigate>About Us</a></li>
                    <li><a href="{{ route('contact') }}" wire:navigate>Contact</a></li>
                    <li><a href="{{ route('home') }}#pricing" wire:navigate>Pricing</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Careers</a></li>
                </ul>
            </div>

            {{-- Newsletter --}}
            <div class="col-lg-4">
                <div class="footer-heading">Stay Updated</div>
                <p class="footer-newsletter-text">
                    Get laundry tips and exclusive offers straight to your inbox.
                </p>
                <div class="d-flex gap-2">
                    <input type="email" class="form-control footer-input" placeholder="your@email.com">
                    <button class="btn btn-primary px-3 fw-semibold btn-subscribe">Subscribe</button>
                </div>
                <div class="mt-4">
                    <div class="footer-heading" style="font-size:0.85rem;">Contact</div>
                    @if($contactPhone)
                        <div class="footer-contact-item">&#128222; {{ $contactPhone }}</div>
                    @endif
                    @if($contactEmail)
                        <div class="footer-contact-item">&#9993; {{ $contactEmail }}</div>
                    @endif
                    @if($contactAddress)
                        <div class="footer-contact-item">&#128205; {{ $contactAddress }}</div>
                    @endif
                </div>
            </div>
        </div>

        <hr class="footer-divider">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div class="footer-copy">&copy; {{ date('Y') }} {{ $siteName }}. {{ $footerText }}</div>
            <div class="d-flex gap-3 footer-legal">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
