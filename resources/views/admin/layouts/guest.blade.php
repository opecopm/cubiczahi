<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} &mdash; Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <a href="{{ route('admin.login') }}" wire:navigate class="navbar-brand navbar-brand-autodark">
                <h1 class="h3">{{ config('app.name', 'Laravel') }}</h1>
            </a>
        </div>
        {{ $slot }}
    </div>
</div>
</body>
</html>
