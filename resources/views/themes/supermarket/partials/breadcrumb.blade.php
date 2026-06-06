@php
    $variant = $variant ?? 'dark';
    $isLight = $variant === 'light';
    $linkColor = $isLight ? 'rgba(255,255,255,0.78)' : '#6b7280';
    $activeColor = $isLight ? '#ffffff' : '#064e3b';
    $separatorColor = $isLight ? 'rgba(255,255,255,0.5)' : '#d1d5db';

    // Automatically build dynamic nested breadcrumbs if $items is empty/default and CMS $page context exists
    if ((empty($items) || (count($items) === 1 && in_array(($items[0]['label'] ?? ''), ['About', 'Contact']))) && isset($page) && $page instanceof \Modules\CMS\Models\Page) {
        $items = [];
        $currentPage = $page;
        while ($currentPage) {
            array_unshift($items, [
                'label' => $currentPage->title,
                'url' => lroute('cms.page', ['slug' => $currentPage->full_slug])
            ]);
            $currentPage = $currentPage->parent;
        }
    } else {
        $items = $items ?? [];
    }
@endphp

<nav aria-label="breadcrumb" class="mb-3">
    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; font-size: 0.95rem; font-weight: 600;">
        <a href="{{ lroute('home') }}" wire:navigate class="text-decoration-none" style="color: {{ $linkColor }};">{{ __('nav.home') }}</a>

        @foreach($items as $item)
            @php
                $label = is_array($item) ? ($item['label'] ?? '') : $item;
                $url = is_array($item) ? ($item['url'] ?? null) : null;
                $active = $loop->last || blank($url);
            @endphp

            <span style="color: {{ $separatorColor }};">/</span>

            @if($active)
                <span aria-current="page" style="color: {{ $activeColor }}; font-weight: 800;">{{ $label }}</span>
            @else
                <a href="{{ $url }}" wire:navigate class="text-decoration-none" style="color: {{ $linkColor }};">{{ $label }}</a>
            @endif
        @endforeach
    </div>
</nav>
