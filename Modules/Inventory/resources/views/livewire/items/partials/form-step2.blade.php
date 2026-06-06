<div class="row g-3">
    <div class="col-12">
        @php
            $sell = ($sell_price !== null && $sell_price !== '') ? number_format((float) $sell_price, 2) : '—';
            $purchase = ($purchase_price !== null && $purchase_price !== '') ? number_format((float) $purchase_price, 2) : '—';
            $cur = trim((string) ($currency_code ?? ''));
            $cur = $cur !== '' ? $cur : '—';
            $mainPriceInfo = "Sell: {$sell} {$cur} | Purchase: {$purchase} {$cur}";
        @endphp
        <label class="form-label">Main Price (Step 1)</label>
        <input type="text" class="form-control" value="{{ $mainPriceInfo }}" disabled>
    </div>

    <div class="col-12">
        @if ($item->has_variants)
            @livewire('inventory::items.variant-generator', ['itemId' => $item->id], key('variant-generator-'.$item->id))
        @else
            <div class="text-muted">This item has no variations.</div>
        @endif
    </div>

    @if (! $item->has_variants)
        <div class="col-12 d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary" wire:click="goToStep(1)">Back</button>
            <button type="button" class="btn btn-primary" wire:click="goToStep(3)">Next</button>
        </div>
    @endif
</div>
