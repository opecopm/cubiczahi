@extends('themes.supermarket.layouts.guest')

@section('content')
    {{-- ── Marquee Announcement ── --}}
    <div class="top-marquee">
        <div class="marquee-content">
            {!! __('home.marquee') !!}
        </div>
    </div>

    <livewire:customer.layout.navigation />

    <div class="container-fluid" style="padding-top: 2rem; padding-bottom: 4rem;">

        @php
            $bannerItems = ($heroBanner && $heroBanner->items->isNotEmpty()) ? $heroBanner->items : collect();
        @endphp
        <div id="homeHeroCarousel" class="carousel slide hero-carousel mb-5" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @if($bannerItems->isNotEmpty())
                    @foreach($bannerItems as $index => $item)
                        <button type="button" data-bs-target="#homeHeroCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" {!! $index === 0 ? 'aria-current="true"' : '' !!} aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                @else
                    <button type="button" data-bs-target="#homeHeroCarousel" data-bs-slide-to="0" class="active"
                        aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#homeHeroCarousel" data-bs-slide-to="1"
                        aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#homeHeroCarousel" data-bs-slide-to="2"
                        aria-label="Slide 3"></button>
                @endif
            </div>
            <div class="carousel-inner">
                @if($bannerItems->isNotEmpty())
                    @php
                        $badgeClasses = ['bg-primary', 'bg-danger', 'bg-warning text-dark'];
                        $btnClasses = ['btn-primary', 'btn-light', 'btn-primary'];
                        $btnStyles = ['', 'color: #1e293b;', ''];
                    @endphp
                    @foreach($bannerItems as $index => $item)
                        @php
                            $styleIdx = $index % 3;
                            $badgeClass = $badgeClasses[$styleIdx];
                            $btnClass = $btnClasses[$styleIdx];
                            $btnStyle = $btnStyles[$styleIdx];
                            
                            // Parse image URL
                            $imageUrl = media_url($item->image);
                        @endphp
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}"
                            style="background-image: url('{{ $imageUrl }}');">
                            <div class="hero-content">
                                @if($item->subtitle)
                                    <span class="badge {{ $badgeClass }} mb-3"
                                        style="width: fit-content; font-size: 1rem; padding: 0.5em 1em;">{{ $item->subtitle }}</span>
                                @endif
                                
                                @if($item->title)
                                    <h2>{!! $item->title !!}</h2>
                                @endif
                                
                                @if($item->content)
                                    <p class="fs-5 mb-4 max-w-lg">{!! $item->content !!}</p>
                                @endif
                                
                                {{-- Buttons --}}
                                @if(!empty($item->buttons) && is_array($item->buttons))
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach(collect($item->buttons)->sortBy('sort_order') as $btnIdx => $btn)
                                            @php
                                                $currBtnClass = $btnClass;
                                                $currBtnStyle = $btnStyle;
                                                if ($btnIdx > 0) {
                                                    $currBtnClass = 'btn-outline-light';
                                                    $currBtnStyle = '';
                                                }
                                                $label = $btn['label'] ?? '';
                                                if (is_array($label)) {
                                                    $label = $label[app()->getLocale()] ?? $label['en'] ?? reset($label) ?? '';
                                                }
                                            @endphp
                                            @if(!empty($label) && !empty($btn['url']))
                                                <a href="{{ $btn['url'] }}" wire:navigate class="btn {{ $currBtnClass }} btn-lg fw-bold"
                                                    style="width: fit-content; border-radius: 50rem; padding: 0.75rem 2rem; {{ $currBtnStyle }}">{{ $label }}</a>
                                            @endif
                                        @endforeach
                                    </div>
                                @elseif($item->link)
                                    <a href="{{ $item->link }}" wire:navigate class="btn {{ $btnClass }} btn-lg fw-bold"
                                        style="width: fit-content; border-radius: 50rem; padding: 0.75rem 2rem; {{ $btnStyle }}">{{ __('home.hero_1_cta') }}</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="carousel-item active"
                        style="background-image: url('https://images.unsplash.com/photo-1498049794561-7780e7231661?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');">
                        <div class="hero-content">
                            <span class="badge bg-primary mb-3"
                                style="width: fit-content; font-size: 1rem; padding: 0.5em 1em;">{{ __('home.hero_1_badge') }}</span>
                            <h2>{!! __('home.hero_1_title') !!}</h2>
                            <p class="fs-5 mb-4 max-w-lg">{{ __('home.hero_1_sub') }}</p>
                            <a href="{{ lroute('catalog.index') }}" wire:navigate class="btn btn-primary btn-lg fw-bold"
                                style="width: fit-content; border-radius: 50rem; padding: 0.75rem 2rem;">{{ __('home.hero_1_cta') }}</a>
                        </div>
                    </div>
                    <div class="carousel-item"
                        style="background-image: url('https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');">
                        <div class="hero-content">
                            <span class="badge bg-danger mb-3"
                                style="width: fit-content; font-size: 1rem; padding: 0.5em 1em;">{{ __('home.hero_2_badge') }}</span>
                            <h2>{!! __('home.hero_2_title') !!}</h2>
                            <p class="fs-5 mb-4 max-w-lg">{{ __('home.hero_2_sub') }}</p>
                            <a href="{{ lroute('catalog.index') }}" wire:navigate class="btn btn-light btn-lg fw-bold"
                                style="width: fit-content; border-radius: 50rem; padding: 0.75rem 2rem; color: #1e293b;">{{ __('home.hero_2_cta') }}</a>
                        </div>
                    </div>
                    <div class="carousel-item"
                        style="background-image: url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');">
                        <div class="hero-content">
                            <span class="badge bg-warning text-dark mb-3"
                                style="width: fit-content; font-size: 1rem; padding: 0.5em 1em;">{{ __('home.hero_3_badge') }}</span>
                            <h2>{!! __('home.hero_3_title') !!}</h2>
                            <p class="fs-5 mb-4 max-w-lg">{{ __('home.hero_3_sub') }}</p>
                            <a href="{{ lroute('catalog.index') }}" wire:navigate class="btn btn-primary btn-lg fw-bold"
                                style="width: fit-content; border-radius: 50rem; padding: 0.75rem 2rem;">{{ __('home.hero_3_cta') }}</a>
                        </div>
                    </div>
                @endif
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        {{-- ── Circular Categories ── --}}
        <div class="mb-5 text-center">
            <div class="mb-3 px-2">
                <h3 class="fw-bold mb-1" style="color: #1e293b;">{{ __('home.shop_by_category') }}</h3>
                <a href="{{ lroute('catalog.index') }}" wire:navigate
                    class="text-primary text-decoration-none fw-semibold">{{ __('home.view_all_categories') }}</a>
            </div>
            <div class="cat-circle-wrapper">
                @foreach ([['icon' => '📱', 'key' => 'electronics'], ['icon' => '👗', 'key' => 'fashion'], ['icon' => '💄', 'key' => 'beauty'], ['icon' => '🛋️', 'key' => 'home'], ['icon' => '🛒', 'key' => 'supermarket'], ['icon' => '🍼', 'key' => 'baby'], ['icon' => '⚽', 'key' => 'sports'], ['icon' => '📚', 'key' => 'books'], ['icon' => '⌚', 'key' => 'jewelry'], ['icon' => '🕌', 'key' => 'islamic']] as $cat)
                    <a href="{{ lroute('catalog.index') }}" wire:navigate class="cat-circle-item">
                        <div class="cat-circle">{!! $cat['icon'] !!}</div>
                        <div class="cat-circle-title">{{ __('home.cat_' . $cat['key']) }}</div>
                    </a>
                @endforeach
            </div>
        </div>

        @php
            $defaultCurrency = \Modules\Business\Models\Currency::where('is_default', 1)->first();
            $getRandomProducts = function () {
                return \Modules\Inventory\Models\Item::where('status', 'active')
                    ->where('type', 'service')
                    ->with(['primaryImage', 'prices', 'category'])
                    ->inRandomOrder()
                    ->take(5)
                    ->get();
            };
            $iconEmojis = [
                '📱',
                '💻',
                '🎧',
                '⌚',
                '📺',
                '🛏️',
                '☕',
                '🍽️',
                '🍳',
                '🪴',
                '🧥',
                '👟',
                '👜',
                '🕶️',
                '👖',
                '🧴',
                '💅',
                '🧼',
                '✨',
                '🪒',
            ];
        @endphp

        {{-- ── Thematic Products: Electronics ── --}}
        <div class="mb-5 text-center">
            <h3 class="fw-bold mb-3 px-2" style="color: #1e293b;">{{ __('home.latest_electronics') }}</h3>
            <div class="h-product-list">
                @foreach ($getRandomProducts() as $i => $item)
                    <div style="min-width: 260px; max-width: 260px; text-align: left;">
                        @include('themes.supermarket.partials.product-card', [
                            'item' => $item,
                            'variant' => ['emerald', 'amber', 'teal', 'blue', 'purple'][$i % 5],
                            'emoji' => $iconEmojis[$i % 5],
                            'keyPrefix' => 'home-elec',
                            'defaultCurrency' => $defaultCurrency ?? null,
                        ])
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Thematic Products: Home & Kitchen ── --}}
        <div class="mb-5 text-center">
            <h3 class="fw-bold mb-3 px-2" style="color: #1e293b;">{{ __('home.home_kitchen') }}</h3>
            <div class="h-product-list">
                @foreach ($getRandomProducts() as $i => $item)
                    <div style="min-width: 260px; max-width: 260px; text-align: left;">
                        @include('themes.supermarket.partials.product-card', [
                            'item' => $item,
                            'variant' => ['emerald', 'amber', 'teal', 'blue', 'purple'][$i % 5],
                            'emoji' => $iconEmojis[5 + ($i % 5)],
                            'keyPrefix' => 'home-home',
                            'defaultCurrency' => $defaultCurrency ?? null,
                        ])
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Thematic Products: Fashion ── --}}
        <div class="mb-5 text-center">
            <h3 class="fw-bold mb-3 px-2" style="color: #1e293b;">{{ __('home.trending_fashion') }}</h3>
            <div class="h-product-list">
                @foreach ($getRandomProducts() as $i => $item)
                    <div style="min-width: 260px; max-width: 260px; text-align: left;">
                        @include('themes.supermarket.partials.product-card', [
                            'item' => $item,
                            'variant' => ['emerald', 'amber', 'teal', 'blue', 'purple'][$i % 5],
                            'emoji' => $iconEmojis[10 + ($i % 5)],
                            'keyPrefix' => 'home-fash',
                            'defaultCurrency' => $defaultCurrency ?? null,
                        ])
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Thematic Products: Beauty ── --}}
        <div class="mb-5 text-center">
            <h3 class="fw-bold mb-3 px-2" style="color: #1e293b;">{{ __('home.beauty_care') }}</h3>
            <div class="h-product-list">
                @foreach ($getRandomProducts() as $i => $item)
                    <div style="min-width: 260px; max-width: 260px; text-align: left;">
                        @include('themes.supermarket.partials.product-card', [
                            'item' => $item,
                            'variant' => ['emerald', 'amber', 'teal', 'blue', 'purple'][$i % 5],
                            'emoji' => $iconEmojis[15 + ($i % 5)],
                            'keyPrefix' => 'home-beauty',
                            'defaultCurrency' => $defaultCurrency ?? null,
                        ])
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Features Bar ── --}}
        <div class="features-bar mb-5">
            <div class="row g-4">
                @foreach ([['icon' => '🚚', 'key' => 'delivery'], ['icon' => '🛡️', 'key' => 'payment'], ['icon' => '⭐', 'key' => 'quality'], ['icon' => '🎁', 'key' => 'offers']] as $feat)
                    <div class="col-md-6 col-lg-3">
                        <div class="feature-item">
                            <div class="feature-icon">{!! $feat['icon'] !!}</div>
                            <div>
                                <div class="fw-bold" style="color: #1e293b; font-size: 1.05rem;">
                                    {{ __('home.feature_' . $feat['key'] . '_title') }}
                                </div>
                                <div class="text-muted small" style="line-height: 1.3;">
                                    {{ __('home.feature_' . $feat['key'] . '_desc') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Testimonials ── --}}
        @if ($testimonials->isNotEmpty())
            <div class="text-center mb-4 mt-5">
                <div class="section-label">{{ __('home.happy_customers') }}</div>
                <h2 class="fw-bold" style="color: #1e293b;">{{ __('home.what_people_say') }}</h2>
            </div>
            <div class="row g-4 mb-5">
                @foreach ($testimonials as $index => $t)
                    @php
                        $avatarColors = ['avatar--emerald', 'avatar--amber', 'avatar--teal', 'avatar--blue', 'avatar--purple'];
                        $avatarColor = $avatarColors[$index % count($avatarColors)];
                        
                        $name = $t->getTranslation('name', app()->getLocale()) ?: $t->getTranslation('name', 'en');
                        $role = $t->getTranslation('designation', app()->getLocale()) ?: $t->getTranslation('designation', 'en');
                        $text = $t->getTranslation('message', app()->getLocale()) ?: $t->getTranslation('message', 'en');
                        $initial = mb_strtoupper(mb_substr($name ?? 'T', 0, 1, 'UTF-8'), 'UTF-8');
                    @endphp
                    <div class="col-md-4">
                        <div class="testimonial-card h-100 d-flex flex-column"
                            style="background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
                            <div class="stars mb-3 text-warning fs-5">
                                @if ($t->rating)
                                    @for ($i = 1; $i <= 5; $i++)
                                        {!! $i <= $t->rating ? '&#9733;' : '&#9734;' !!}
                                    @endfor
                                @else
                                    &#9733;&#9733;&#9733;&#9733;&#9733;
                                @endif
                            </div>
                            <p class="text-muted review-text mb-4 flex-grow-1" style="font-style: italic;">
                                "{{ $text }}"
                            </p>
                            <div class="d-flex align-items-center gap-3 mt-auto">
                                @if($t->image)
                                    <img src="{{ media_url($t->image) }}" class="rounded-circle border" style="width: 48px; height: 48px; object-fit: cover;" alt="Avatar">
                                @else
                                    <div class="avatar-circle {{ $avatarColor }} fw-bold fs-5">{{ $initial }}</div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark reviewer-name">{{ $name }}</div>
                                    @if($role)
                                        <div class="text-muted reviewer-role small">{{ $role }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    @include(theme_view('partials.footer'))
@endsection
