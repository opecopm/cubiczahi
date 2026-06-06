<div>
    @if ($showButton)
        <button type="button"
                class="btn btn-primary"
                wire:click="$dispatch('openMediaPicker', { allowedTypes: {{ json_encode($allowedTypes) }}, multiple: {{ $multiple ? 'true' : 'false' }}, maxSelection: {{ $maxSelection }}, usage: '{{ $usage }}' })">
            <i class="ti ti-photo"></i>
            {{ $buttonText }}
            @if ($multiple && count($selectedMediaIds) > 0)
                <span class="badge bg-white text-primary ms-1">{{ count($selectedMediaIds) }}</span>
            @endif
        </button>

        @if (!$showPickerModal && count($selectedMediaIds) > 0)
            <div class="mt-2 d-flex flex-wrap gap-1">
                @foreach ($selectedMedia as $media)
                    <div class="position-relative" style="width: 50px; height: 50px;">
                        @if ($media->hasMedia('media'))
                            <img src="{{ $media->getFirstMediaUrl('media', 'thumb') }}"
                                 class="img-thumbnail"
                                 style="width: 50px; height: 50px; object-fit: cover;"
                                 alt="{{ $media->alt_text ?? $media->name }}">
                        @else
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                                 style="width: 50px; height: 50px;">
                                <i class="ti ti-file"></i>
                            </div>
                        @endif
                        <button type="button"
                                class="btn btn-xs btn-danger position-absolute rounded-circle"
                                style="top: -5px; right: -5px; width: 20px; height: 20px; padding: 0;"
                                wire:click="removeFromSelection({{ $media->id }})">
                            <i class="ti ti-x" style="font-size: 10px;"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    {{-- Modal --}}
    @if ($showPickerModal)
        <div class="modal-backdrop fade show" style="display: block;"></div>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="z-index: 1050;">
            <div class="modal-dialog modal-lg modal-xl" role="document" style="max-width: 95vw;">
                <div class="modal-content">
                    {{-- Header --}}
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-photo"></i>
                            Media Library
                        </h5>
                        <button type="button" class="btn-close" wire:click="closePicker"></button>
                    </div>

                    {{-- Toolbar --}}
                    <div class="modal-header border-bottom">
                        <div class="row g-3 w-100">
                            {{-- Search --}}
                            <div class="col-md-4">
                                <div class="input-icon">
                                    <input type="text"
                                           class="form-control"
                                           placeholder="Search media..."
                                           wire:model.live.debounce.300ms="search">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                </div>
                            </div>

                            {{-- Filter by Kind --}}
                            <div class="col-md-3">
                                <select class="form-select" wire:model="filterKind">
                                    <option value="">All Types</option>
                                    @foreach ($kinds as $kind)
                                        <option value="{{ $kind }}">{{ ucfirst($kind) }}s</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Sort --}}
                            <div class="col-md-3">
                                <select class="form-select" wire:model="sortBy">
                                    <option value="created_at">Date (Newest)</option>
                                    <option value="name">Name (A-Z)</option>
                                    <option value="size">Size</option>
                                </select>
                            </div>

                            {{-- Per Page --}}
                            <div class="col-md-2">
                                <select class="form-select" wire:model="perPage">
                                    <option value="12">12 per page</option>
                                    <option value="24">24 per page</option>
                                    <option value="48">48 per page</option>
                                    <option value="96">96 per page</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Selection Actions Bar --}}
                    @if ($hasSelection)
                        <div class="bg-primary text-white px-3 py-2 d-flex justify-content-between align-items-center">
                            <span>
                                <strong>{{ $selectionCount }}</strong> item(s) selected
                                @if ($maxSelection > 0)
                                    (max: {{ $maxSelection }})
                                @endif
                            </span>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-light" wire:click="clearSelection">
                                    Clear All
                                </button>
                                <button type="button" class="btn btn-outline-light" wire:click="selectAll">
                                    Select All Visible
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Body --}}
                    <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                        @if ($media->count() > 0)
                            <div class="row g-2">
                                @foreach ($media as $item)
                                    <div class="col-6 col-md-4 col-lg-3 col-xl-2" wire:key="media-item-{{ $item->id }}">
                                        <div class="card card-sm h-100 {{ $this->isSelected($item->id) ? 'border-primary border-2' : '' }}"
                                             wire:click="toggleMediaSelection({{ $item->id }})"
                                             style="cursor: pointer;">
                                            <div class="card-body p-2">
                                                {{-- Preview --}}
                                                <div class="position-relative mb-2">
                                                    @if ($item->kind === 'image' && $item->hasMedia('media'))
                                                        <img src="{{ $item->getFirstMediaUrl('media', 'thumb') }}"
                                                             class="img-fluid"
                                                             style="height: 120px; width: 100%; object-fit: cover;"
                                                             alt="{{ $item->alt_text ?? $item->name }}">
                                                    @elseif ($item->kind === 'video')
                                                        <div class="bg-dark d-flex align-items-center justify-content-center"
                                                             style="height: 120px;">
                                                            <i class="ti ti-video text-white" style="font-size: 3rem;"></i>
                                                        </div>
                                                    @else
                                                        <div class="bg-secondary d-flex align-items-center justify-content-center"
                                                             style="height: 120px;">
                                                            <i class="ti ti-file text-white" style="font-size: 3rem;"></i>
                                                        </div>
                                                    @endif

                                                    {{-- Selection Checkbox --}}
                                                    <div class="position-absolute top-0 end-0 p-1">
                                                        @if ($this->isSelected($item->id))
                                                            <span class="badge bg-primary rounded-circle p-1">
                                                                <i class="ti ti-check"></i>
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- File Type Badge --}}
                                                    <div class="position-absolute bottom-0 start-0 p-1">
                                                        <span class="badge bg-dark bg-opacity-75">
                                                            {{ strtoupper($item->extension ?? $item->kind) }}
                                                        </span>
                                                    </div>
                                                </div>

                                                {{-- Info --}}
                                                <div class="text-truncate" style="font-size: 0.75rem;">
                                                    <strong class="d-block">{{ $item->name }}</strong>
                                                    <span class="text-muted">
                                                        {{ $item->width ?? '?' }}x{{ $item->height ?? '?' }}
                                                        @if ($item->size)
                                                            • {{ number_format($item->size / 1024, 1) }}KB
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Pagination --}}
                            <div class="mt-3 d-flex justify-content-center">
                                {{ $media->links('pagination::bootstrap-5') }}
                            </div>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="ti ti-photo-off" style="font-size: 4rem;"></i>
                                <p class="mt-3">No media found</p>
                                @if ($search)
                                    <p>Try a different search term</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Upload Section --}}
                    <div class="modal-header border-top">
                        <h6 class="mb-0">
                            <i class="ti ti-upload"></i>
                            Upload New Media
                        </h6>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 align-items-end">
                            <div class="col">
                                <label class="form-label">Upload Files</label>
                                <input type="file"
                                       class="form-control"
                                       wire:model="newFiles"
                                       multiple
                                       accept="{{ implode(',', array_map(fn($t) => $t === 'image' ? 'image/*' : $t, $allowedTypes)) }}">
                                @error('newFiles.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-auto">
                                <button type="button"
                                        class="btn btn-success"
                                        wire:click="uploadNewFiles"
                                        wire:loading.attr="disabled"
                                        wire:target="newFiles">
                                    <span wire:loading wire:target="uploadNewFiles" class="spinner-border spinner-border-sm me-1"></span>
                                    <i class="ti ti-upload"></i>
                                    Upload
                                </button>
                            </div>
                        </div>
                        @if (session()->has('success'))
                            <div class="alert alert-success mt-2 mb-0">
                                <i class="ti ti-check"></i>
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer">
                        <div class="d-flex justify-content-between w-100">
                            <div>
                                @if ($hasSelection)
                                    <span class="text-muted">
                                        {{ $selectionCount }} of {{ $media->total() }} selected
                                    </span>
                                @endif
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-secondary" wire:click="closePicker">
                                    Cancel
                                </button>
                                <button type="button"
                                        class="btn btn-primary"
                                        wire:click="confirmSelection"
                                        @if (!$hasSelection) disabled @endif>
                                    <i class="ti ti-check"></i>
                                    Select {{ $multiple ? 'Items' : 'Item' }}
                                    @if ($hasSelection)
                                        ({{ $selectionCount }})
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
