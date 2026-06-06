@php
    $user = auth()->user();
    $unreadCount = $user ? $user->unreadNotifications()->count() : 0;
    $notifications = $user ? $user->unreadNotifications()->latest()->limit(8)->get() : collect();
@endphp

<div class="nav-item dropdown">
    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" aria-label="{{ __('Notifications') }}">
        <i class="ti ti-bell"></i>
        @if($unreadCount > 0)
            <span class="badge bg-danger ms-1">{{ $unreadCount }}</span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="min-width: 320px;">
        <div class="dropdown-header d-flex align-items-center justify-content-between">
            <span>{{ __('Notifications') }}</span>
            @if($unreadCount > 0)
                <a type="button"
                        wire:click.stop="markAllRead"
                        class="btn btn-link btn-sm p-0 text-primary">
                    {{ __('Mark all as read') }}
                </a>
            @endif
        </div>

        @if($notifications->isEmpty())
            <div class="dropdown-item text-secondary">{{ __('No notifications') }}</div>
        @else
            @foreach($notifications as $notification)
                @php
                    $data    = (array) ($notification->data ?? []);
                    $message = $data['message'] ?? $notification->type;
                @endphp
                <a class="dropdown-item d-flex gap-2 fw-semibold"
                   href="{{ route('admin.notifications.open', $notification->id) }}">
                    <span class="avatar avatar-xs bg-primary"></span>
                    <div class="flex-fill">
                        <div class="text-truncate">{{ $message }}</div>
                        <div class="small text-secondary">{{ $notification->created_at?->diffForHumans() }}</div>
                    </div>
                </a>
            @endforeach
        @endif
    </div>
</div>
