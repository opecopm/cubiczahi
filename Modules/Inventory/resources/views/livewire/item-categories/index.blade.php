<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Item Category Management',
        'breadcrumbs' => [
            [
                'label' => 'Categories',
                'active' => true,
            ],
        ],
    ])
        @can('create_item_categories')
            @slot('actions')
                <div class="d-flex gap-2">
                    <button wire:click="translateCategories" wire:loading.attr="disabled" class="btn btn-outline-info">
                        <span wire:loading wire:target="translateCategories" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        <i class="ti ti-language me-1" wire:loading.remove wire:target="translateCategories"></i>
                        Translate Categories
                    </button>
                    <button wire:click="openModal" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>
                        Add New Category
                    </button>
                </div>
            @endslot
        @endcan
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards mb-3">
                <div class="col-sm-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="bg-blue text-white avatar">{{ $categories->total() }}</span></div>
                                <div class="col"><div class="text-secondary">All Categories</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('components.tabler.alerts')

            <div class="card">
                <div class="card-header">
                    <div class="row g-2 w-100">
                        <div class="col">
                            <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search...">
                        </div>
                        @foreach($model->filterable as $field => $meta)
                            @if($field === 'status')
                                @continue
                            @endif
                            <div class="col">
                                @if(($meta['type'] ?? null) === 'select')
                                    <select class="form-select" wire:model.live="filters.{{ $field }}">
                                        <option value="">{{ ucfirst(str_replace('_',' ',$field)) }}: All</option>
                                        @foreach(($meta['options'] ?? []) as $key => $val)
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        @endforeach
                        <div class="col-auto">
                            <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>ID @include('components.table.sort', ['field' => 'id'])</th>
                                <th>Code @include('components.table.sort', ['field' => 'code'])</th>
                                <th>Name @include('components.table.sort', ['field' => 'name'])</th>
                                <th>Parent Category</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories->where('parent_id', null) as $category)
                                @include('inventory::livewire.item-categories.partials.item-category-row', ['category' => $category, 'level' => 0])
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($categories->hasPages())
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <small class="text-secondary">
                            Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }}
                        </small>
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal modal-blur fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $updateMode ? 'Edit Category' : 'Add New Category' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="code" wire:model="code">
                            @error('code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="name_en" class="form-label">Category Name (EN)</label>
                            <input type="text" class="form-control" id="name_en" wire:model="name.en">
                            @error('name.en')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        @foreach($active_languages as $lang)
                        <div class="col-md-6">
                            <label for="name_{{ $lang }}" class="form-label">Category Name ({{ strtoupper($lang) }})</label>
                            <input type="text" class="form-control" id="name_{{ $lang }}" wire:model="name.{{ $lang }}">
                            @error('name.'.$lang)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        @endforeach
                            @php
                                function renderCategoryOptions($categories, $prefix = '') {
                                    foreach ($categories as $cat) {
                                        echo '<option value="'.$cat->id.'">'.$prefix.$cat->name.'</option>';
                                        if ($cat->children && $cat->children->count()) {
                                            renderCategoryOptions($cat->children, $prefix.'— ');
                                        }
                                    }
                                }
                            @endphp
                        <div class="col-md-12">
                            <label for="parent_id" class="form-label">Parent Category</label>
                            <select id="parent_id" class="form-select" wire:model="parent_id">
                                <option value="">Select Parent Category</option>
                                @php renderCategoryOptions($parent_categories); @endphp
                            </select>
                            @error('parent_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeModal">Cancel</button>
                    @if ($updateMode)
                        <button type="button" class="btn btn-primary" wire:click="update">Save changes</button>
                    @else
                        <button type="button" class="btn btn-primary" wire:click="store">Save</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
