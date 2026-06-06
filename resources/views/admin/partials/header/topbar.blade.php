<header class="navbar navbar-expand-md d-print-none d-none d-lg-flex sticky-top" style="z-index:1030;backdrop-filter:blur(8px);">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-brand navbar-brand-autodark d-none d-lg-flex pe-0 pe-md-3">
            <a href="{{ route('admin.dashboard') }}" wire:navigate
               class="text-decoration-none d-flex align-items-center gap-2">
                <span class="avatar avatar-sm rounded-2 flex-shrink-0"
                      style="background:linear-gradient(135deg,#206bc4 0%,#4dabf7 100%);">
                    <i class="ti ti-wash-dry-2 text-white"></i>
                </span>
                <span class="d-none d-sm-flex flex-column lh-1">
                    <span class="fw-bold text-body" style="font-size:.95rem;letter-spacing:-.2px;">
                        {{ config('app.name', 'Open Laundry') }}
                    </span>
                </span>
            </a>
        </div>

        <div class="navbar-nav flex-row order-md-last">
            <div class="d-none d-md-flex me-3">
                @include('admin.partials.header.notifications')
            </div>
            @include('admin.partials.header.top-right')
        </div>
    </div>
</header>
