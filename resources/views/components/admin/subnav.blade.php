@props([
    'items' => [],
])

<div class="d-print-none border-bottom">
    <div class="container-fluid">
        <ul class="nav nav-tabs">
            @foreach ($items as $item)
                @php
                    $label = $item['label'] ?? $item['title'] ?? '';
                    $href = $item['href'] ?? ($item['route'] ?? null ? route($item['route']) : '#');

                    $active = (bool) ($item['active'] ?? false);
                    $activeWhen = $item['activeWhen'] ?? null;

                    if (!$active && $activeWhen) {
                        $patterns = is_array($activeWhen) ? $activeWhen : [$activeWhen];
                        $active = request()->routeIs(...$patterns);
                    }

                    $navigate = (bool) ($item['navigate'] ?? true);
                @endphp

                <li class="nav-item">
                    <a class="nav-link{{ $active ? ' active' : '' }}" href="{{ $href }}" @if($navigate) wire:navigate @endif>
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
