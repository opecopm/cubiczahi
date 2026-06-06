<div class="nav-item dropdown">
    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="{{ __('Open user menu') }}">
        <span class="avatar avatar-sm">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </span>
        <div class="d-none d-xl-block ps-2">
            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
            <div class="mt-1 small text-secondary">{{ auth()->user()->email }}</div>
        </div>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <a href="{{ route('admin.profile') }}" wire:navigate class="dropdown-item">
            {{ __('Profile') }}
        </a>
        <div class="dropdown-divider"></div>
        <button wire:click="logout" class="dropdown-item">
            {{ __('Log Out') }}
        </button>
    </div>
</div>
