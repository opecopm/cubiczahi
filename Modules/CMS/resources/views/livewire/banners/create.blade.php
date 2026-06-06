<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Create Banner</h2>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('admin.cms.banners.index') }}" class="btn btn-outline-secondary">
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
                    <form wire:submit.prevent="store">

                        <div class="mb-3">
                            <label class="form-label">Banner Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                wire:model="name" placeholder="Enter banner name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Banner Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                wire:model.lazy="slug"
                                wire:keydown="$set('manualSlug', true)"
                                placeholder="Auto-generated or enter manually">
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" wire:model="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr>
                        <h6 class="fw-bold mb-3">Banner Items</h6>

                        @foreach($items as $index => $item)
                            <div class="card border mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Title</label>
                                            <input type="text" class="form-control"
                                                wire:model="items.{{ $index }}.title" placeholder="Enter title">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Subtitle</label>
                                            <input type="text" class="form-control"
                                                wire:model="items.{{ $index }}.subtitle" placeholder="Enter subtitle">
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <label class="form-label">Content</label>
                                            <textarea class="form-control" rows="2"
                                                wire:model="items.{{ $index }}.content" placeholder="Enter content"></textarea>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Image</label>
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
                                            @if(isset($item['image_preview']))
                                                <img src="{{ $item['image_preview'] }}" alt="preview" class="img-fluid mt-2"
                                                    style="max-height: 120px;">
                                            @endif
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Link</label>
                                            <input type="text" class="form-control"
                                                wire:model="items.{{ $index }}.link" placeholder="https://example.com">
                                        </div>

                                        <div class="col-md-12 mb-2">
                                            <label class="form-label">Buttons</label>
                                            @foreach($item['buttons'] ?? [] as $btnIndex => $button)
                                                <div class="row mb-2 border rounded p-2">
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control"
                                                            wire:model="items.{{ $index }}.buttons.{{ $btnIndex }}.label"
                                                            placeholder="Button Label">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control"
                                                            wire:model="items.{{ $index }}.buttons.{{ $btnIndex }}.url"
                                                            placeholder="Button URL">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="number" class="form-control"
                                                            wire:model="items.{{ $index }}.buttons.{{ $btnIndex }}.sort_order"
                                                            placeholder="Sort Order">
                                                    </div>
                                                    <div class="col-md-2 text-end">
                                                        <button type="button" class="btn btn-sm btn-ghost-danger"
                                                            wire:click="removeButton({{ $index }}, {{ $btnIndex }})">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                                wire:click="addButton({{ $index }})">
                                                + Add Button
                                            </button>
                                        </div>

                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Sort Order</label>
                                            <input type="number" class="form-control"
                                                wire:model="items.{{ $index }}.sort_order">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Status</label>
                                            <select class="form-control"
                                                wire:model="items.{{ $index }}.status">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="text-end mt-2">
                                        <button type="button" class="btn btn-sm btn-ghost-danger"
                                            wire:click="removeItem({{ $index }})">
                                            <i class="ti ti-trash me-1"></i> Remove Item
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary" wire:click="addItem">
                                <i class="ti ti-plus me-1"></i> Add Banner Item
                            </button>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i> Save Banner
                            </button>
                            <a href="{{ route('admin.cms.banners.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @livewire('mediagallery::media-picker', ['showButton' => false, 'multiple' => false, 'allowedTypes' => ['image']])
</div>
