@php
    $sellPrice    = $item->prices?->where('price_type','sell')->first();
    $currencyCode = $defaultCurrency?->code ?? $sellPrice?->currency ?? '';
    $currencySymbol = $defaultCurrency?->symbol_left ?? $defaultCurrency?->symbol_right ?? '';
    $detailUrl    = lroute('catalog.show', $item->slug ?: $item->id);
    
    $variant = $variant ?? 'emerald';
    $emoji = $emoji ?? '&#128230;'; // Package emoji
    $keyPrefix = $keyPrefix ?? 'pc';
@endphp

<div class="svc-card d-flex flex-column h-100 position-relative">
    <a href="{{ $detailUrl }}" wire:navigate class="d-block text-decoration-none text-dark" style="flex-grow: 1;">
        {{-- Image area --}}
        <div class="svc-card__img-wrap">
            @if($item->primaryImage)
                <img src="{{ $item->primaryImage->url }}" alt="{{ $item->getTranslation('name','en') }}">
            @else
                <div class="svc-card__no-img svc-card__no-img--{{ $variant }}">
                    @if($item->icon_class)
                        <i class="{{ $item->icon_class }}" style="font-size:3.5rem;"></i>
                    @else
                        {!! $emoji !!}
                    @endif
                </div>
            @endif

            {{-- Price badge --}}
            @if($sellPrice)
                <div class="svc-card__price-badge">
                    {{ $currencySymbol }}{{ number_format($sellPrice->price, 2) }} {{ $currencyCode }}
                </div>
            @else
                <div class="svc-card__price-badge" style="background:rgba(22,163,74,0.85);">
                    On request
                </div>
            @endif

            {{-- Category badge on image --}}
            @if($item->category)
                <div class="svc-card__cat-badge">{{ $item->category->name }}</div>
            @endif
        </div>

        {{-- Body --}}
        <div class="svc-card__body">
            <div class="svc-card__icon-row">
                @if($item->icon_class)
                    <span class="svc-card__icon svc-card__icon--{{ $variant }}">
                        <i class="{{ $item->icon_class }}"></i>
                    </span>
                @endif
                <h5 class="svc-card__title">
                    {{ $item->getTranslation('name','en') }}
                </h5>
            </div>

            @if($item->short_description)
                <p class="svc-card__desc">
                    {!! \Illuminate\Support\Str::limit(strip_tags($item->short_description), 90) !!}
                </p>
            @endif
        </div>
    </a>
    
    <div class="svc-card__footer mt-auto d-flex justify-content-center" style="border-top:1px solid #f1f3f5; padding:12px 16px;">
        <livewire:customer.catalog.add-to-cart :item="$item" :compact="true" :wire:key="$keyPrefix . '-' . $item->id" />
    </div>
</div>
