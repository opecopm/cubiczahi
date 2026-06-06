<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Brand Management',
        'breadcrumbs' => [
            [
                'label' => 'Brands',
                'active' => true,
            ],
        ],
    ])
        @can('create_brands')
            @slot('actions')
                <button wire:click="openModal" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>
                    Add New Brand
                </button>
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
                                <div class="col-auto"><span class="bg-blue text-white avatar">{{ $brands->total() }}</span></div>
                                <div class="col"><div class="text-secondary">All Brands</div></div>
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
                                <th>Name @include('components.table.sort', ['field' => 'name'])</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($brands as $brand)
                                <tr>
                                    <td class="text-secondary">{{ $brand->id }}</td>
                                    <td>{{ $brand->name }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @can('update_brands')
                                                    <button wire:click="edit({{ $brand->id }})" class="dropdown-item">Edit</button>
                                                @endcan
                                                @can('delete_brands')
                                                    <button wire:click="confirmDelete({{ $brand->id }})" wire:confirm="Are you sure you want to delete this brand?" class="dropdown-item text-danger">Delete</button>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-secondary py-4">No brands found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($brands->hasPages())
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <small class="text-secondary">
                            Showing {{ $brands->firstItem() ?? 0 }} to {{ $brands->lastItem() ?? 0 }} of {{ $brands->total() }}
                        </small>
                        {{ $brands->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $updateMode ? 'Edit Brand' : 'Add New Brand' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="name" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="name" wire:model="name">
                            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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

    @include('admin.livewire.partials.delete-confirmation-modal')
</div>
