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
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @endif

    @vite(['resources/views/themes/laundry-one/assets/css/app.css', 'resources/views/themes/laundry-one/assets/js/app.js'])
    
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
    </style>
    @else
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @endif

    @stack('styles')
</head>
<body>
    {{ $slot ?? '' }}
    @yield('content')
    @stack('scripts')
</body>
</html>
