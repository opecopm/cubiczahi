<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top" style="padding: 1rem 0;">
    <div class="container-fluid">

        {{-- Brand / Logo --}}
        <a class="navbar-brand fw-bold fs-3 me-lg-4" href="{{ lroute('home') }}" wire:navigate>
            <span class="text-primary" style="background: linear-gradient(135deg, var(--bs-primary), #10b981); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">
                &#10024; {{ config('app.name', 'MegaStore') }}
            </span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse align-items-center" id="mainNav">

            {{-- Main Links --}}
            <ul class="navbar-nav me-auto gap-lg-2 fw-semibold">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') || request()->routeIs('ar.home') ? 'text-primary' : 'text-dark' }}"
                       href="{{ lroute('home') }}" wire:navigate>
                        {{ __('nav.home') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('catalog.*') || request()->routeIs('ar.catalog.*') ? 'text-primary' : 'text-dark' }}"
                       href="{{ lroute('catalog.index') }}" wire:navigate>
                        {{ __('nav.shop') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger d-flex align-items-center gap-1"
                       href="{{ lroute('catalog.deals') }}" wire:navigate>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tag-fill" viewBox="0 0 16 16">
                            <path d="M2 1a1 1 0 0 0-1 1v4.586a1 1 0 0 0 .293.707l7 7a1 1 0 0 0 1.414 0l4.586-4.586a1 1 0 0 0 0-1.414l-7-7A1 1 0 0 0 6.586 1H2zm4 3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                        </svg>
                        {{ __('nav.deals') }}
                    </a>
                </li>
            </ul>

            {{-- Search Bar --}}
            <form action="{{ lroute('catalog.index') }}" method="GET"
                  class="d-flex mx-auto w-100 my-3 my-lg-0" style="max-width: 600px;">
                <div class="input-group nav-search-group">
                    <select name="category"
                            class="form-select shadow-none bg-transparent text-muted nav-search-select">
                        <option value="">{{ __('nav.all_categories') }}</option>
                        @foreach([
                            'electronics'  => __('home.cat_electronics'),
                            'fashion'      => __('home.cat_fashion'),
                            'beauty'       => __('home.cat_beauty'),
                            'home-kitchen' => __('home.cat_home'),
                            'supermarket'  => __('home.cat_supermarket'),
                            'baby-kids'    => __('home.cat_baby'),
                            'sports'       => __('home.cat_sports'),
                            'books'        => __('home.cat_books'),
                            'jewelry'      => __('home.cat_jewelry'),
                            'islamic'      => __('home.cat_islamic'),
                        ] as $slug => $label)
                            <option value="{{ $slug }}" {{ request('category') == $slug ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control shadow-none bg-transparent nav-search-input"
                           placeholder="{{ __('nav.search') }}">
                    <button class="btn shadow-none nav-search-btn" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                    </button>
                </div>
            </form>

            {{-- Right Actions --}}
            <div class="d-flex align-items-center gap-3 ms-lg-auto mt-3 mt-lg-0">

                {{-- Language Switcher — DB-driven, links to /ar/current-page for SEO --}}
                @if($activeLanguages->count() > 1)
                <div class="d-flex align-items-center gap-1 me-2" style="font-size: 0.9rem;">
                    @foreach($activeLanguages as $lang)
                        @if(! $loop->first)
                            <span class="text-muted mx-1">|</span>
                        @endif
                        @if($lang->code === $currentLocale)
                            <span class="text-primary fw-bold">{{ $lang->name }}</span>
                        @else
                            <a href="{{ locale_url($lang->code) }}"
                               class="text-muted text-decoration-none fw-semibold">
                                {{ $lang->name }}
                            </a>
                        @endif
                    @endforeach
                </div>
                @endif

                {{-- Cart --}}
                <livewire:layout.cart-counter />

                {{-- Account --}}
                @auth
                    <a href="{{ lroute('customer.dashboard') }}" wire:navigate
                       class="text-decoration-none text-dark d-flex align-items-center gap-2">
                        <div style="width:42px; height:42px; background:#f1f5f9; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.2rem; transition: background 0.2s;"
                             onmouseover="this.style.background='#e2e8f0'"
                             onmouseout="this.style.background='#f1f5f9'">
                            👤
                        </div>
                        <span class="d-none d-xl-inline fw-semibold" style="font-size: 0.95rem;">
                            {{ __('nav.account') }}
                        </span>
                    </a>
                @else
                    <a href="{{ lroute('customer.login') }}" wire:navigate
                       class="text-decoration-none text-dark d-flex align-items-center gap-2">
                        <div style="width:42px; height:42px; background:#f1f5f9; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.2rem; transition: background 0.2s;"
                             onmouseover="this.style.background='#e2e8f0'"
                             onmouseout="this.style.background='#f1f5f9'">
                            👤
                        </div>
                        <span class="d-none d-xl-inline fw-semibold" style="font-size: 0.95rem;">
                            {{ __('nav.sign_in') }}
                        </span>
                    </a>
                @endauth

            </div>
        </div>
    </div>
</nav>
