<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        @if($banner->items->isEmpty())
                            Add Banner: <span class="text-primary ms-2">{{ $banner->getTranslation('name', 'en') }}</span>
                        @else
                            Edit Banner: <span class="text-primary ms-2">{{ $banner->getTranslation('name', 'en') }}</span>
                        @endif
                    </h2>
                </div>
                <div class="col-auto ms-auto d-flex align-items-center gap-2">
                    <select class="form-select border-secondary text-secondary fw-semibold" wire:model.live="activeLocale" style="width: auto; height: 36px; padding-top: 4px; padding-bottom: 4px;">
                        @foreach ($activeLanguages as $lang)
                            <option value="{{ $lang->code }}">
                                🌐 {{ $lang->name }} ({{ strtoupper($lang->code) }})
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('admin.cms.banners.index') }}" class="btn btn-outline-secondary" style="height: 36px; display: inline-flex; align-items: center;">
                        <i class="ti ti-arrow-left me-1"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @include('components.tabler.alerts')

            <div class="card">
                <div class="card-body">
                    <form wire:submit.prevent="update">
                        <!-- Slide Selection Dropdown -->
                        <div class="card mb-4 border border-primary-subtle shadow-sm bg-primary-lt">
                            <div class="card-body">
                                <div class="row align-items-end g-3">
                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-bold text-dark mb-1">
                                            <i class="ti ti-layers-difference me-1 text-primary"></i> Select Slide to Edit
                                        </label>
                                        <select class="form-select form-select-lg border-primary" wire:model.live="selectedItemIndex">
                                            @foreach ($items as $idx => $itm)
                                                <option value="{{ $idx }}">
                                                    Slide {{ $idx + 1 }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-12 text-md-end d-flex gap-2 justify-content-md-end justify-content-start align-items-end">
                                        <button type="button" class="btn btn-primary" wire:click="addItem">
                                            <i class="ti ti-plus me-1"></i> Add New Slide
                                        </button>
                                        @if (count($items) > 1)
                                            <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirm('Are you sure you want to delete this slide?') || event.stopImmediatePropagation()"
                                                wire:click="removeItem({{ $selectedItemIndex }})">
                                                <i class="ti ti-trash me-1"></i> Delete Slide
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Banner Item Form Fields -->
                        @if (isset($items[$selectedItemIndex]))
                            @php
                                $index = $selectedItemIndex;
                                $item = $items[$selectedItemIndex];
                                $activeDirection = $activeLanguages->where('code', $activeLocale)->first()?->direction ?? 'ltr';
                            @endphp
                            <div class="card border border-2 border-primary-subtle shadow-sm mb-4" wire:key="slide-card-{{ $index }}-{{ $activeLocale }}">
                                <div class="card-body">
                                    <div class="mb-3 pb-2 border-bottom">
                                        <h4 class="card-title fw-bold text-primary mb-0">
                                            <i class="ti ti-edit me-1"></i> Slide {{ $index + 1 }} Details
                                        </h4>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold text-secondary">Title ({{ strtoupper($activeLocale) }})</label>
                                            <input type="text" class="form-control" dir="{{ $activeDirection }}"
                                                wire:model="items.{{ $index }}.title.{{ $activeLocale }}" placeholder="Enter title">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold text-secondary">Subtitle ({{ strtoupper($activeLocale) }})</label>
                                            <input type="text" class="form-control" dir="{{ $activeDirection }}"
                                                wire:model="items.{{ $index }}.subtitle.{{ $activeLocale }}"
                                                placeholder="Enter subtitle">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-semibold text-secondary">Content ({{ strtoupper($activeLocale) }})</label>
                                            <textarea class="form-control" rows="3" dir="{{ $activeDirection }}" wire:model="items.{{ $index }}.content.{{ $activeLocale }}"
                                                placeholder="Enter content"></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold text-secondary">Image</label>
                                            <input type="file" class="form-control"
                                                wire:model="items.{{ $index }}.image">
                                            <input type="hidden" wire:model="items.{{ $index }}.image_path">
                                            <input type="hidden" wire:model="items.{{ $index }}.image_preview">
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    wire:click="$dispatch('openMediaPicker', { allowedTypes: ['image'], multiple: false, usage: 'banner-image-{{ $index }}' })">
                                                    <i class="ti ti-photo me-1"></i> Choose from Media Gallery
                                                </button>
                                            </div>
                                            @if (isset($item['image_preview']))
                                                <img src="{{ $item['image_preview'] }}" alt="preview"
                                                    class="img-fluid mt-2 rounded border" style="max-height: 120px;">
                                            @elseif(isset($item['image_path']))
                                                @php
                                                    $previewUrl = str_starts_with($item['image_path'], 'media-content/') ? asset($item['image_path']) : asset('storage/' . $item['image_path']);
                                                @endphp
                                                <img src="{{ $previewUrl }}" alt="preview"
                                                    class="img-fluid mt-2 rounded border" style="max-height: 120px;">
                                            @endif
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold text-secondary">Link</label>
                                            <input type="text" class="form-control"
                                                wire:model="items.{{ $index }}.link"
                                                placeholder="https://example.com">
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-semibold text-secondary">Buttons</label>
                                            @foreach ($item['buttons'] ?? [] as $btnIndex => $button)
                                                <div class="row mb-2 border rounded p-2 bg-light-lt align-items-center" wire:key="button-row-{{ $index }}-{{ $btnIndex }}-{{ $activeLocale }}">
                                                    <div class="col-md-4 col-12 mb-2 mb-md-0">
                                                        <input type="text" class="form-control" dir="{{ $activeDirection }}"
                                                            wire:model="items.{{ $index }}.buttons.{{ $btnIndex }}.label.{{ $activeLocale }}"
                                                            placeholder="Button Label ({{ strtoupper($activeLocale) }})">
                                                    </div>
                                                    <div class="col-md-4 col-12 mb-2 mb-md-0">
                                                        <input type="text" class="form-control"
                                                            wire:model="items.{{ $index }}.buttons.{{ $btnIndex }}.url"
                                                            placeholder="Button URL">
                                                    </div>
                                                    <div class="col-md-2 col-6">
                                                        <input type="number" class="form-control"
                                                            wire:model="items.{{ $index }}.buttons.{{ $btnIndex }}.sort_order"
                                                            placeholder="Sort Order">
                                                    </div>
                                                    <div class="col-md-2 col-6 text-end">
                                                        <button type="button" class="btn btn-sm btn-ghost-danger w-100"
                                                            wire:click="removeButton({{ $index }}, {{ $btnIndex }})">
                                                            <i class="ti ti-trash me-1"></i> Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-1"
                                                wire:click="addButton({{ $index }})">
                                                <i class="ti ti-plus me-1"></i> Add Button
                                            </button>
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-semibold text-secondary">Sort Order</label>
                                            <input type="number" class="form-control"
                                                wire:model="items.{{ $index }}.sort_order">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-semibold text-secondary">Status</label>
                                            <select class="form-select" wire:model="items.{{ $index }}.status">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>
                                @if($banner->items->isEmpty())
                                    Save Banner
                                @else
                                    Update Banner
                                @endif
                            </button>
                            <a href="{{ route('admin.cms.banners.index') }}"
                                class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @livewire('mediagallery::media-picker', ['showButton' => false, 'multiple' => false, 'allowedTypes' => ['image']])
</div>
