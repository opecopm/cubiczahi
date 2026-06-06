{{-- Filter / Quick-Nav Bar --}}
@if ($item->has_variants && $item->variants->isNotEmpty())
    <div class="mb-4 d-flex align-items-center gap-2">
        <label class="text-muted small fw-semibold mb-0">Jump to:</label>
        <select class="form-select form-select-sm w-auto"
                onchange="document.getElementById(this.value)?.scrollIntoView({ behavior: 'smooth', block: 'start' }); this.value='';">
            <option value="">— select section —</option>
            <option value="section-item-images">Product Images</option>
            @foreach ($item->variants as $variant)
                <option value="section-variant-{{ $variant->id }}-images">{{ $variant->label }}</option>
            @endforeach
        </select>
    </div>
@endif

{{-- Product Images using MediaManager Component --}}
<div id="section-item-images">
    @livewire('media-manager', [
        'entityId' => $item->id,
        'entityType' => 'item',
        'mediaType' => 'image',
        'title' => 'Product Images',
        'description' => 'Upload product images for the gallery',
        'allowMultiple' => true,
        'allowPrimary' => true,
        'acceptedFormats' => 'image/*',
        'maxFileSize' => 2048,
    ], key('media-manager-item-' . $item->id . '-images'))
</div>

{{-- Variant Images using MediaManager Component --}}
@if ($item->has_variants)
    <hr class="my-4">
    <h4 class="mb-4">Variant Images</h4>
    @foreach ($item->variants as $variant)
        <div id="section-variant-{{ $variant->id }}-images" class="mb-4">
            @livewire('media-manager', [
                'entityId' => $variant->id,
                'entityType' => 'variant',
                'mediaType' => 'image',
                'title' => 'Images for ' . $variant->label,
                'description' => 'Upload images specific to this variant',
                'allowMultiple' => true,
                'allowPrimary' => true,
                'acceptedFormats' => 'image/*',
                'maxFileSize' => 2048,
            ], key('media-manager-variant-' . $variant->id . '-images'))
        </div>
    @endforeach
@endif

<div class="d-flex justify-content-between mt-3">
    <button type="button" class="btn btn-outline-secondary" wire:click="goToStep(2)">Back</button>
    <button type="button" class="btn btn-primary" wire:click="saveStep3">Next</button>
</div>
