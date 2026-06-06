@if (($item['type'] ?? null) === 'section')
    <li class="nav-item nav-section-header mt-2">
        <div style="border-top: 1px solid rgba(255,255,255,.08); margin: 0 .75rem .4rem;"></div>
        <span class="nav-section-label" style="font-size:.65rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:rgba(255,255,255,.35); padding-left:.75rem;">
            {{ $item['title'] }}
        </span>
    </li>
@else
@php
    $hasChildren = !empty($item['children']);
    $isActive = (bool) ($item['active'] ?? false);
    $icon = $item['icon'] ?? null;
@endphp

<li class="nav-item{{ $hasChildren ? ' dropdown' : '' }}{{ $isActive ? ' active' : '' }}">
    @if ($hasChildren)
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="{{ $isActive ? 'true' : 'false' }}">
            @if ($icon)
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <i class="ti {{ $icon }}"></i>
                </span>
            @endif
            <span class="nav-link-title">{{ $item['title'] }}</span>
        </a>
        <div class="dropdown-menu">
            @foreach ($item['children'] as $child)
                <a href="{{ $child['href'] }}" class="dropdown-item{{ !empty($child['active']) ? ' active' : '' }}" wire:navigate>
                    @if (!empty($child['icon']))
                        <span class="me-2 opacity-75"><i class="ti {{ $child['icon'] }} fs-5"></i></span>
                    @endif
                    {{ $child['title'] }}
                </a>
            @endforeach
        </div>
    @else
        <a class="nav-link" href="{{ $item['href'] }}" wire:navigate>
            @if ($icon)
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <i class="ti {{ $icon }}"></i>
                </span>
            @endif
            <span class="nav-link-title">{{ $item['title'] }}</span>
        </a>
    @endif
</li>
@endif
