<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} &mdash; Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .container-xl {
            padding-left: 5px;
            padding-right: 5px;
        }
        /* Sidebar flex layout — desktop only so Bootstrap collapse works on mobile */
        @media (min-width: 992px) {
            .sidebar-collapse {
                display: flex !important;
                flex-direction: column;
                height: calc(100vh - 72px);
            }
        }
    </style>
</head>
<body class="antialiased layout-vertical">
<div class="page">
    <livewire:admin.layout.navigation />

    <div class="page-wrapper">
        @include('admin.partials.header.topbar')

        @hasSection('subnav')
            @yield('subnav')
        @endif

        @if (isset($header))
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            {{ $header }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="page-body">
            <div class="container-xl">
                @if (isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </div>
        </div>

        @include('admin.partials.footer.bottom')
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
@stack('js')
</body>
</html>
