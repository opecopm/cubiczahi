@php
    $footerServices = \Modules\Inventory\Models\Item::where('status','active')
        ->where('type','service')
        ->select('id','name','slug')
        ->orderBy('name->'.app()->getLocale())
        ->take(6)
        ->get();

    // Fetch dynamic Web Settings
    $siteName     = web_setting('site_name', config('app.name', 'DetergentShop'));
    $footerAbout  = web_setting('footer_about', __('footer.tagline'));
    $footerText   = web_setting('footer_text', __('footer.all_rights'));
    
    // Logos
    $logoModel    = \Modules\CMS\Models\WebSetting::where('key', 'footer_logo')->first();
    $footerLogoUrl = $logoModel ? $logoModel->getFirstMediaUrl('footer_logo') : null;

    // Contact
    $contactEmail   = web_setting('contact_email', 'hello@detergentshop.com');
    $contactPhone   = web_setting('contact_phone', '+1 (800) DETERGENT');
    $contactAddress = web_setting('contact_address');

    // Footer Company Menu (CMS-driven)
    $footerCompanyMenu = \Modules\CMS\Models\Menu::where('slug', 'footer-company')
        ->with(['items' => function ($q) {
            $q->where('is_visible', true)->orderBy('order', 'asc');
        }])
        ->first();

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
    <div class="container-fluid">
        <div class="row g-5">
            {{-- Brand --}}
            <div class="col-lg-4">
                <div class="footer-brand mb-3">
                    @if($footerLogoUrl)
                        <img src="{{ $footerLogoUrl }}" alt="{{ $siteName }}" style="max-height: 48px; object-fit: contain;">
                    @else
                        &#10024; {{ $siteName }}
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

            {{-- Products --}}
            <div class="col-6 col-lg-2">
                <div class="footer-heading">{{ __('footer.shop_heading') }}</div>
                <ul class="footer-links">
                    @forelse($footerServices as $svc)
                        <li>
                            @if($svc->slug)
                                <a href="{{ lroute('catalog.show', $svc->slug) }}" wire:navigate>
                                    {{ $svc->getTranslation('name', app()->getLocale()) }}
                                </a>
                            @else
                                <a href="{{ lroute('catalog.index') }}" wire:navigate>
                                    {{ $svc->getTranslation('name', app()->getLocale()) }}
                                </a>
                            @endif
                        </li>
                    @empty
                        <li><a href="{{ lroute('catalog.index') }}" wire:navigate>{{ __('footer.view_all_products') }}</a></li>
                    @endforelse
                    @if($footerServices->isNotEmpty())
                        <li>
                            <a href="{{ lroute('catalog.index') }}" wire:navigate style="color:#fbbf24; font-weight:600;">
                                {{ __('footer.view_all') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Company --}}
            <div class="col-6 col-lg-2">
                <div class="footer-heading">
                    {{ $footerCompanyMenu
                        ? $footerCompanyMenu->getTranslation('name', app()->getLocale(), false) ?: $footerCompanyMenu->getTranslation('name', 'en', false)
                        : __('footer.company_heading') }}
                </div>
                <ul class="footer-links">
                    @if ($footerCompanyMenu && $footerCompanyMenu->items->isNotEmpty())
                        @foreach ($footerCompanyMenu->items as $menuItem)
                            @if ($menuItem->is_visible)
                                <li>
                                    <a href="{{ $menuItem->url ?: '#' }}"
                                       target="{{ $menuItem->target ?? '_self' }}"
                                       @if(str_starts_with($menuItem->url ?? '', '/') || str_starts_with($menuItem->url ?? '', lroute('home'))) wire:navigate @endif>
                                        {{ $menuItem->getTranslation('title', app()->getLocale(), false) ?: $menuItem->getTranslation('title', 'en', false) }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @else
                        {{-- Static fallback --}}
                        <li><a href="{{ lroute('about') }}" wire:navigate>{{ __('footer.about') }}</a></li>
                        <li><a href="{{ lroute('contact') }}" wire:navigate>{{ __('footer.contact') }}</a></li>
                        <li><a href="#">{{ __('footer.blog') }}</a></li>
                        <li><a href="#">{{ __('footer.careers') }}</a></li>
                    @endif
                </ul>
            </div>

            {{-- Newsletter --}}
            <div class="col-lg-4">
                <div class="footer-heading">{{ __('footer.newsletter_heading') }}</div>
                <p class="footer-newsletter-text">
                    {{ __('footer.newsletter_text') }}
                </p>
                <div class="d-flex gap-2">
                    <input type="email" class="form-control footer-input" placeholder="{{ __('footer.email_placeholder') }}">
                    <button class="btn btn-subscribe px-3 fw-semibold text-white">{{ __('footer.subscribe') }}</button>
                </div>
                <div class="mt-4">
                    <div class="footer-heading" style="font-size:0.85rem;">{{ __('footer.contact_heading') }}</div>
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
                <a href="{{ lroute('legal.privacy') }}" wire:navigate>{{ __('footer.privacy') }}</a>
                <a href="{{ lroute('legal.terms') }}" wire:navigate>{{ __('footer.terms') }}</a>
                <a href="{{ lroute('legal.refund') }}" wire:navigate>{{ __('footer.refund') }}</a>
            </div>
        </div>
    </div>
</footer>
