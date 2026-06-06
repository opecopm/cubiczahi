<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Blog Categories</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <button class="btn btn-primary" wire:click="openCreateModal()">
                        <i class="ti ti-plus me-1"></i> Add Root Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="flex-grow-1 me-3">
                            <input type="text" class="form-control" wire:model.debounce.500ms="search" placeholder="Search categories...">
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @php($locale = app()->getLocale())
                    @if($categories->isEmpty())
                        <p class="text-muted">No categories yet. Create your first one.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($categories as $cat)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $cat->getTranslation('name', $locale) ?? $cat->getTranslation('name', $defaultLocale) }}</strong>
                                            <span class="badge bg-light text-dark ms-2">Slug: {{ $cat->slug }}</span>
                                            @if(!$cat->status)
                                                <span class="badge bg-warning text-dark ms-2">Inactive</span>
                                            @endif
                                        </div>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" wire:click="openCreateModal({{ $cat->id }})">Add Child</button>
                                            <button class="btn btn-sm btn-outline-secondary" wire:click="openEditModal({{ $cat->id }})">Edit</button>
                                            <button class="btn btn-sm btn-outline-danger"
                                                wire:click="confirmDelete({{ $cat->id }})"
                                                wire:confirm="Are you sure you want to delete this category?">Delete</button>
                                        </div>
                                    </div>
                                    @if($cat->children && $cat->children->count())
                                        @include('cms::livewire.blog-categories.partials.tree', ['nodes' => $cat->children])
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal modal-blur fade @if($showModal) show d-block @endif" tabindex="-1" role="document"
        style="@if($showModal) background: rgba(0, 0, 0, 0.5); @endif"
        @if($showModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editMode ? 'Edit Category' : 'Add Category' }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        @php($default = $defaultLocale)
                        <ul class="nav nav-tabs" id="categoryLangTabs" role="tablist">
                            @foreach($languages as $index => $lang)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link @if($index===0) active @endif"
                                        id="tab-{{ $lang['code'] }}"
                                        data-bs-toggle="tab"
                                        data-bs-target="#pane-{{ $lang['code'] }}"
                                        type="button" role="tab"
                                        aria-controls="pane-{{ $lang['code'] }}"
                                        aria-selected="{{ $index===0 ? 'true' : 'false' }}">
                                        {{ strtoupper($lang['code']) }}
                                        @if($lang['is_default'])<span class="badge bg-info ms-1">Default</span>@endif
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content mt-3" id="categoryLangTabsContent">
                            @foreach($languages as $index => $lang)
                                @php($code = $lang['code'])
                                <div class="tab-pane fade @if($index===0) show active @endif"
                                    id="pane-{{ $code }}" role="tabpanel" aria-labelledby="tab-{{ $code }}">
                                    <div class="mb-3">
                                        <label class="form-label" for="name_{{ $code }}">Name ({{ strtoupper($code) }})</label>
                                        <input type="text" class="form-control @error('name.' . $code) is-invalid @enderror"
                                            id="name_{{ $code }}"
                                            @if ($code == 'en') wire:model.live="name.{{ $code }}" @else wire:model.defer="name.{{ $code }}" @endif
                                            placeholder="Enter name">
                                        @error('name.' . $code) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="slug">Slug</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                        id="slug" wire:model.live="slug" placeholder="auto-generated if empty">
                                    @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" wire:model.defer="status" id="statusCheck">
                                    <label class="form-check-label" for="statusCheck">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="parentId">Parent Category</label>
                            <select class="form-control @error('parentId') is-invalid @enderror" id="parentId" wire:model.defer="parentId">
                                <option value="">None (root)</option>
                                @foreach($categories as $root)
                                    <option value="{{ $root->id }}">{{ $root->getTranslation('name', app()->getLocale()) ?? $root->getTranslation('name', $defaultLocale) }}</option>
                                @endforeach
                            </select>
                            @error('parentId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @error('delete') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" wire:click="$set('showModal', false)">Cancel</button>
                        <button type="submit" class="btn btn-primary">{{ $editMode ? 'Update' : 'Save' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
