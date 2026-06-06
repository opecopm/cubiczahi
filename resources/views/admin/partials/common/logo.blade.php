<a href="{{ route('admin.dashboard') }}" wire:navigate class="navbar-brand text-decoration-none d-flex align-items-center gap-2 py-2">
    <span class="avatar avatar-sm rounded-2 flex-shrink-0"
          style="background: linear-gradient(135deg, #206bc4 0%, #4dabf7 100%);">
        <i class="ti ti-wash-dry-2 text-white fs-4"></i>
    </span>
    <span class="d-flex flex-column lh-1">
        <span class="fw-bold text-white fs-4" style="letter-spacing: -.3px;">
            {{ config('app.name', 'Open Laundry') }}
        </span>
        <span class="text-muted" style="font-size: .65rem; letter-spacing: .08em; text-transform: uppercase;">
            Management
        </span>
    </span>
</a>
