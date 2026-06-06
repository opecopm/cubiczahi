@php
    $dir   = $currentLang?->direction ?? 'ltr';
    $isRtl = $dir === 'rtl';
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.seo')

    @if($isRtl)
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    @else
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @endif

    {{-- hreflang: tells Google about all language versions of this page --}}
    @foreach($activeLanguages as $lang)
        <link rel="alternate" hreflang="{{ $lang->code }}" href="{{ locale_url($lang->code) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ locale_url(config('app.locale', 'en')) }}">

    @vite(['resources/views/themes/supermarket/assets/css/app.css', 'resources/views/themes/supermarket/assets/js/app.js'])

    @if($isRtl)
    <style>
        body { font-family: 'Tajawal', sans-serif !important; }
        .ms-auto    { margin-right: auto !important; margin-left: 0 !important; }
        .me-auto    { margin-left:  auto !important; margin-right: 0 !important; }
        .ms-lg-auto { margin-right: auto !important; margin-left: 0 !important; }
        .me-lg-4    { margin-left: 1.5rem !important; margin-right: 0 !important; }
        .ms-2       { margin-right: .5rem !important;  margin-left: 0 !important; }
        .me-2       { margin-left:  .5rem !important;  margin-right: 0 !important; }
        .ms-3       { margin-right: 1rem !important;   margin-left: 0 !important; }
        .me-3       { margin-left:  1rem !important;   margin-right: 0 !important; }
        .text-start { text-align: right !important; }
        .text-end   { text-align: left  !important; }
        .dropdown-menu { text-align: right; }
        .navbar-nav { padding-right: 0; }
        .navbar-toggler { margin-left: 0; margin-right: auto; }
        /* Sidebar border flip */
        .account-sidebar-nav .nav-link { border-left: none !important; border-right: 3px solid transparent; }
        .account-sidebar-nav .nav-link.active { border-right-color: #059669; }
    </style>
    @else
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
    @endif

    @stack('styles')
</head>
<body class="antialiased">
<div class="wrapper">
    <livewire:customer.layout.navigation />

    <div class="page-wrapper">
        @hasSection('header')
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">@yield('header')</div>
                    </div>
                    @hasSection('breadcrumb')
                        <div class="row mt-2">
                            <div class="col">@yield('breadcrumb')</div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="page-body">
            <div class="container-xl">
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </div>
    </div>
</div>
</body>
</html>
