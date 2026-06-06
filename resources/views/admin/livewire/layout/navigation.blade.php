<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark bg-dark" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        @include('admin.partials.common.logo')
        <div class="navbar-nav flex-row d-lg-none align-items-center gap-1">
            @include('admin.partials.header.notifications')
            @include('admin.partials.header.top-right')
        </div>
        <div class="collapse navbar-collapse sidebar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3 flex-grow-1 overflow-y-auto">
                @each('admin.partials.navbar.dropdown-item', $menu, 'item')
            </ul>

            {{-- Logout footer --}}
            <div class="p-3" style="border-top: 1px solid rgba(255,255,255,.08); flex-shrink:0;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="avatar avatar-sm rounded-2 flex-shrink-0"
                          style="background:rgba(255,255,255,.08);">
                        <i class="ti ti-user text-white"></i>
                    </span>
                    <div class="flex-fill overflow-hidden">
                        <div class="text-white fw-medium text-truncate small">{{ auth()->user()->name }}</div>
                        <div class="text-truncate" style="font-size:.7rem; color:rgba(255,255,255,.4);">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <button wire:click="logout"
                        class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-2"
                        style="background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); color:rgba(255,255,255,.7);">
                    <i class="ti ti-logout"></i>
                    <span>Sign out</span>
                </button>
            </div>
        </div>
    </div>
</aside>
