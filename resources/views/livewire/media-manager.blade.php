<div class="media-manager-container" wire:key="media-manager-{{ $entityType }}-{{ $entityId }}">
    <div class="card mb-4">
        {{-- Header --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="card-title mb-0">
                    @if ($title)
                        {{ $title }}
                    @else
                        {{ ucfirst($mediaType) }}s
                        <span class="badge bg-primary-lt text-primary">{{ $totalMedia }}</span>
                    @endif
                </h3>
                @if ($description && $ui !== 'compact')
                    <small class="text-muted d-block mt-1">{{ $description }}</small>
                @endif
            </div>
            <div class="card-actions d-flex align-items-center gap-2">
                @if ($ui === 'compact')
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            wire:click="toggleCompactOpen">
                        @if ($compactOpen)
                            Hide
                        @else
                            Manage
                        @endif
                    </button>
                @endif
                @if ($ui !== 'compact' && $hasMedia)
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            wire:click="toggleViewMode"
                            aria-label="Toggle view mode"
                            title="Toggle view mode">
                        @if ($viewMode === 'grid')
                            <i class="ti ti-list-details"></i>
                        @else
                            <i class="ti ti-layout-grid"></i>
                        @endif
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body">
            @if ($ui === 'compact' && ! $compactOpen)
                <div class="d-flex justify-content-between align-items-center text-muted">
                    <small>{{ $totalMedia }} {{ $mediaType }}(s)</small>
                    @if ($description)
                        <small>{{ $description }}</small>
                    @endif
                </div>
            @else
            {{-- Existing Media --}}
            @if ($showExistingMedia && $hasMedia)
                <div class="mb-4">
                    @if ($ui === 'compact')
                        <div class="table-responsive">
                            <table class="table table-sm table-vcenter mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;"></th>
                                        <th style="width: 140px;">Uploaded</th>
                                        <th class="text-end" style="width: 120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($media as $item)
                                        <tr wire:key="media-compact-{{ $item['id'] }}">
                                            <td>
                                                <div class="position-relative d-inline-block">
                                                    @if ($mediaType === 'image' || str_ends_with($item['path'], ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                                                        <span class="avatar avatar-md"
                                                              style="background-image: url('{{ $item['url'] }}')"
                                                              aria-label="{{ $item['name'] }}"></span>
                                                    @else
                                                        <span class="avatar avatar-md bg-light text-secondary d-inline-flex align-items-center justify-content-center"
                                                              aria-label="{{ $item['name'] }}">
                                                            <i class="ti ti-file"></i>
                                                        </span>
                                                    @endif
                                                    @if ($item['is_primary'] && $allowPrimary)
                                                        <span class="badge bg-success position-absolute top-0 start-0 m-1 rounded-pill p-1">
                                                            <i class="ti ti-star fs-5"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-muted">
                                                <small>{{ $item['created_at']?->diffForHumans() ?? '-' }}</small>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-list gap-1 flex-nowrap justify-content-end">
                                                    @if ($allowPrimary && !$item['is_primary'])
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-warning"
                                                                wire:click="setPrimary({{ $item['id'] }})"
                                                                title="Set as primary">
                                                            <i class="ti ti-star"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-primary"
                                                            wire:click="editMedia({{ $item['id'] }})"
                                                            title="Edit">
                                                        <i class="ti ti-pencil"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger"
                                                            wire:click="deleteMedia({{ $item['id'] }})"
                                                            wire:confirm="Are you sure you want to delete this {{ $mediaType }}?"
                                                            title="Delete">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                    @if ($viewMode === 'grid')
                        {{-- Grid View --}}
                        <div class="row row-cards">
                            @foreach ($media as $item)
                                <div class="col-6 col-md-3 col-lg-2">
                                    <div class="card card-sm position-relative media-item-card"
                                         wire:key="media-{{ $item['id'] }}">
                                        {{-- Media Display --}}
                                        @if ($mediaType === 'image' || str_ends_with($item['path'], ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                                            <img src="{{ $item['url'] }}"
                                                 class="card-img-top object-cover"
                                                 style="height: 120px; object-fit: cover; cursor: pointer;"
                                                 alt="{{ $item['name'] }}"
                                                 data-bs-toggle="modal"
                                                 data-bs-target="#mediaPreview{{ $item['id'] }}">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                 style="height: 120px;">
                                                <i class="ti ti-file fs-1 text-secondary"></i>
                                            </div>
                                        @endif

                                        {{-- Primary Badge --}}
                                        @if ($item['is_primary'] && $allowPrimary)
                                            <span class="badge bg-success position-absolute top-0 start-0 m-1 d-inline-flex align-items-center gap-1">
                                                <i class="ti ti-star"></i>
                                                Primary
                                            </span>
                                        @endif

                                        {{-- Actions --}}
                                        <div class="card-body p-1 d-flex gap-1 justify-content-center flex-wrap">
                                            @if ($allowPrimary && !$item['is_primary'])
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-warning"
                                                        wire:click="setPrimary({{ $item['id'] }})"
                                                        title="Set as primary">
                                                    <i class="ti ti-star"></i>
                                                </button>
                                            @endif

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    wire:click="editMedia({{ $item['id'] }})"
                                                    title="Edit">
                                                <i class="ti ti-pencil"></i>
                                            </button>

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    wire:click="deleteMedia({{ $item['id'] }})"
                                                    wire:confirm="Are you sure you want to delete this {{ $mediaType }}?"
                                                    title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- List View --}}
                        <div class="table-responsive">
                            <table class="table table-sm table-vcenter mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;"></th>
                                        <th style="width: 140px;">Uploaded</th>
                                        <th class="text-end" style="width: 120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($media as $item)
                                        <tr wire:key="media-list-{{ $item['id'] }}">
                                            <td>
                                                <div class="position-relative d-inline-block">
                                                    @if ($mediaType === 'image' || str_ends_with($item['path'], ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                                                        <span class="avatar avatar-md"
                                                              style="background-image: url('{{ $item['url'] }}')"
                                                              aria-label="{{ $item['name'] }}"></span>
                                                    @else
                                                        <span class="avatar avatar-md bg-light text-secondary d-inline-flex align-items-center justify-content-center"
                                                              aria-label="{{ $item['name'] }}">
                                                            <i class="ti ti-file"></i>
                                                        </span>
                                                    @endif
                                                    @if ($item['is_primary'] && $allowPrimary)
                                                        <span class="badge bg-success position-absolute top-0 start-0 m-1 rounded-pill p-1">
                                                            <i class="ti ti-star fs-5"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-muted">
                                                <small>{{ $item['created_at']?->diffForHumans() ?? '-' }}</small>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-list gap-1 flex-nowrap justify-content-end">
                                                    @if ($allowPrimary && !$item['is_primary'])
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-warning"
                                                                wire:click="setPrimary({{ $item['id'] }})"
                                                                title="Set as primary">
                                                            <i class="ti ti-star"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-primary"
                                                            wire:click="editMedia({{ $item['id'] }})"
                                                            title="Edit">
                                                        <i class="ti ti-pencil"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger"
                                                            wire:click="deleteMedia({{ $item['id'] }})"
                                                            wire:confirm="Are you sure you want to delete this {{ $mediaType }}?"
                                                            title="Delete">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @endif
                </div>
            @elseif ($showExistingMedia)
                <div class="empty">
                    <div class="empty-img">
                        @if ($mediaType === 'image')
                            <i class="ti ti-photo fs-1 text-secondary"></i>
                        @else
                            <i class="ti ti-file fs-1 text-secondary"></i>
                        @endif
                    </div>
                    <p class="empty-title">No {{ $mediaType }}s uploaded yet</p>
                </div>
            @endif

            {{-- Edit Modal --}}
            @if ($editingMediaId)
                <div class="modal-backdrop fade show" style="display: block;"></div>
                <div class="modal modal-blur fade show" style="display: block;" role="dialog" aria-modal="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit {{ ucfirst($mediaType) }}</h5>
                                <button type="button" class="btn-close" wire:click="cancelEdit()" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text"
                                           class="form-control"
                                           wire:model="editData.title"
                                           placeholder="Enter title">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control"
                                              wire:model="editData.description"
                                              rows="3"
                                              placeholder="Enter description"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-link link-secondary" wire:click="cancelEdit()">
                                    Cancel
                                </button>
                                <button type="button" class="btn btn-primary" wire:click="saveMediaEdit()">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Link Existing Media (for variants) --}}
            @if ($allowLinking && $hasAvailableMedia)
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="ti ti-link me-1"></i>
                            Link from Product Gallery
                            <span class="badge bg-primary-lt text-primary">{{ $totalAvailableMedia }}</span>
                        </h5>
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary"
                                wire:click="toggleLinkingForm">
                            @if ($showLinkingForm)
                                <i class="ti ti-chevron-up"></i>
                            @else
                                <i class="ti ti-chevron-down"></i>
                            @endif
                        </button>
                    </div>

                    @if ($showLinkingForm)
                        <div class="alert alert-info mb-3">
                            <small>Select images from the product gallery to link to this variant. You can select multiple images at once.</small>
                        </div>

                        <div class="row g-2 mb-3">
                            @foreach ($availableMedia as $availImg)
                                <div class="col-6 col-md-4 col-lg-2">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               id="media-{{ $availImg['id'] }}"
                                               wire:model="selectedMediaIds"
                                               value="{{ $availImg['id'] }}"
                                               @if ($availImg['is_linked']) disabled @endif>
                                        <label class="form-check-label w-100" for="media-{{ $availImg['id'] }}">
                                            <div class="card card-sm mb-2 position-relative"
                                                 style="opacity: @if ($availImg['is_linked']) 0.6 @else 1 @endif">
                                                <img src="{{ $availImg['url'] }}"
                                                     class="card-img-top"
                                                     style="height: 80px; object-fit: cover;"
                                                     alt="{{ $availImg['name'] }}">
                                                @if ($availImg['is_linked'])
                                                    <span class="badge bg-success position-absolute top-0 start-0 m-1" style="font-size: 0.65rem;">
                                                        <i class="ti ti-check me-1"></i>
                                                        Linked
                                                    </span>
                                                @endif
                                            </div>
                                            <small class="text-truncate d-block">{{ $availImg['title'] ?? $availImg['name'] }}</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button"
                                    class="btn btn-primary"
                                    wire:click="linkSelectedMedia"
                                    wire:loading.attr="disabled"
                                    wire:target="selectedMediaIds,linkSelectedMedia"
                                    @if (empty($selectedMediaIds)) disabled @endif>
                                <span wire:loading wire:target="linkSelectedMedia"
                                      class="spinner-border spinner-border-sm me-1"></span>
                                <i class="ti ti-link me-1"></i>
                                Link Selected (@if (!empty($selectedMediaIds)) {{ count($selectedMediaIds) }} @else 0 @endif)
                            </button>
                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    wire:click="toggleLinkingForm">
                                Cancel
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <div class="border-top pt-4 mt-4">
                <div class="row g-3 align-items-start">
                    <div class="col-12 col-md-6">
                        {{-- WordPress-style Media Library Button --}}
                        <div class="d-flex gap-2">
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    wire:click="openMediaPicker">
                                <i class="ti ti-photo me-1"></i>
                                Browse Media
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        {{-- Upload Form --}}
                        @if ($showUploadForm)
                            <div class="row g-2 align-items-end">
                                <div class="col">
                                    <input type="file"
                                           class="form-control form-control-sm"
                                           wire:model="newFiles"
                                           accept="{{ $acceptedFormats }}"
                                           {{ $allowMultiple ? 'multiple' : '' }}>
                                    @error('newFiles.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-auto">
                                    <button type="button"
                                            class="btn btn-sm btn-primary"
                                            wire:click="uploadMedia"
                                            wire:loading.attr="disabled"
                                            wire:target="newFiles,uploadMedia">
                                        <span wire:loading wire:target="uploadMedia"
                                              class="spinner-border spinner-border-sm me-1"></span>
                                        <i class="ti ti-upload me-1"></i>
                                        Upload
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- WordPress-style Media Picker Modal --}}
            @if ($showMediaPicker)
                @livewire('mediagallery::media-picker', [
                    'allowedTypes' => $mediaPickerConfig['allowedTypes'] ?? ['image'],
                    'multiple' => true,
                    'maxSelection' => 0,
                    'usage' => $entityType,
                    'entityType' => $entityType,
                    'entityId' => $entityId,
                    'showButton' => false,
                    'autoOpen' => true,
                ], key('media-picker-' . $entityType . '-' . $entityId))
            @endif
            @endif
        </div>
    </div>
</div>

@push('styles')
    <style>
        .media-manager-container .media-item-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .media-manager-container .media-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush
