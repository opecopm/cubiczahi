{{--
    EXAMPLE: How to use MediaManager component in your Blade views
    This shows the Step 3 (Images) section of the Items Edit form
--}}

<div class="tab-pane @if ($step == 3) active show @endif" id="tabs-images">
    @if ($step == 3)
        <div class="container-fluid py-4">

            {{-- Title --}}
            <h3 class="mb-4">Manage Product Media</h3>

            {{-- Product Images Section --}}
            <div class="mb-5">
                @livewire('media-manager', [
                    'entityId' => $item->id,
                    'entityType' => 'item',
                    'mediaType' => 'image',
                    'title' => 'Product Images',
                    'description' => 'Upload high-quality product images for the gallery (max 2MB each)',
                    'allowMultiple' => true,
                    'allowPrimary' => true,
                    'acceptedFormats' => 'image/*',
                    'maxFileSize' => 2048,
                ], key('media-manager-item-' . $item->id . '-images'))
            </div>

            {{-- Variant Images Section (if product has variants) --}}
            @if ($item->has_variants)
                <hr class="my-5">
                <h3 class="mb-4">Variant Images</h3>

                @foreach ($item->variants as $variant)
                    <div class="mb-4">
                        <h5 class="mb-3">
                            <span class="badge bg-blue-lt">{{ $variant->label }}</span>
                        </h5>

                        @livewire('media-manager', [
                            'entityId' => $variant->id,
                            'entityType' => 'variant',
                            'mediaType' => 'image',
                            'title' => 'Images for ' . $variant->label,
                            'description' => 'Upload images specific to this variant',
                            'allowMultiple' => true,
                            'allowPrimary' => true,
                            'acceptedFormats' => 'image/*',
                        ], key('media-manager-variant-' . $variant->id . '-images'))
                    </div>
                @endforeach
            @endif

            {{-- Navigation Buttons --}}
            <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                <button type="button" class="btn btn-outline-secondary" wire:click="goToStep(2)">
                    <i class="ti ti-arrow-left"></i>
                    Back to Pricing
                </button>
                <button type="button" class="btn btn-primary" wire:click="saveStep3">
                    Next to Partners
                    <i class="ti ti-arrow-right"></i>
                </button>
            </div>

        </div>
    @endif
</div>
