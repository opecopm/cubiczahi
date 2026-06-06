<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Location Management',
        'breadcrumbs' => [['label' => 'Locations', 'active' => true]],
        'actionItems' => [[
            'type' => 'button',
            'title' => 'Add New Location',
            'wireClick' => 'addLocation',
            'icon' => 'ti ti-plus',
            'class' => 'btn btn-primary',
        ]],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            {{-- Stats --}}
            <div class="row row-deck row-cards mb-3">
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.status', '')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="bg-blue text-white avatar">{{ $locationsCount }}</span></div>
                                <div class="col"><div class="text-secondary">All Locations</div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.status', 'active')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="bg-green text-white avatar">{{ $activeLocationsCount }}</span></div>
                                <div class="col"><div class="text-secondary">Active</div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.status', 'inactive')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="bg-secondary text-white avatar">{{ $inactiveLocationsCount }}</span></div>
                                <div class="col"><div class="text-secondary">Inactive</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="row g-2 w-100">
                        <div class="col">
                            <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search...">
                        </div>
                        @foreach($model->filterable as $field => $meta)
                            <div class="col">
                                @if(($meta['type'] ?? null) === 'select')
                                    <select class="form-select" wire:model.live="filters.{{ $field }}">
                                        <option value="">{{ ucfirst(str_replace('_',' ',$field)) }}: All</option>
                                        @foreach(($meta['options'] ?? []) as $key => $val)
                                            <option value="{{ $key }}">{{ is_array($val) ? ($val['name'] ?? reset($val)) : $val }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" class="form-control" wire:model.live="filters.{{ $field }}" placeholder="{{ ucfirst(str_replace('_',' ',$field)) }}">
                                @endif
                            </div>
                        @endforeach
                        <div class="col-auto">
                            <button wire:click="resetFilters" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>ID @include('components.table.sort', ['field' => 'id'])</th>
                                <th>Name @include('components.table.sort', ['field' => 'name'])</th>
                                <th>Code @include('components.table.sort', ['field' => 'code'])</th>
                                <th>Type @include('components.table.sort', ['field' => 'type'])</th>
                                <th>Parent</th>
                                <th>Status @include('components.table.sort', ['field' => 'status'])</th>
                                <th>Active</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($locations as $loc)
                                <tr>
                                    <td class="text-secondary">{{ $loc->id }}</td>
                                    <td class="fw-bold">{{ $loc->name }}</td>
                                    <td><span class="badge bg-blue-lt">{{ $loc->code }}</span></td>
                                    <td class="text-secondary">{{ \Modules\Business\Models\Location::TYPE_SELECT[$loc->type] ?? ucfirst($loc->type) }}</td>
                                    <td class="text-secondary">{{ $loc->parent?->name ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $loc->status === 'active' ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                            {{ \Modules\Business\Models\Location::STATUS_SELECT[$loc->status] ?? ucfirst($loc->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $loc->is_active ? 'bg-green-lt' : 'bg-secondary-lt' }}">
                                            {{ $loc->is_active ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <button wire:click="edit({{ $loc->id }})" class="dropdown-item">Edit</button>
                                                <button wire:click="confirmDelete({{ $loc->id }})"
                                                        wire:confirm="Are you sure you want to delete this location?"
                                                        class="dropdown-item text-danger">Delete</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-secondary py-4">No locations found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($locations->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $locations->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal modal-blur fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $updateMode ? 'Edit Location' : 'Add New Location' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" wire:model.defer="location.name">
                            @error('location.name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" wire:model.defer="location.code">
                            @error('location.code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company</label>
                            <select class="form-select" wire:model.defer="location.company_id">
                                <option value="">None</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->getTranslation('name', 'en') }}</option>
                                @endforeach
                            </select>
                            @error('location.company_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select class="form-select" wire:model.defer="location.type">
                                <option value="">Select Type</option>
                                @foreach(\Modules\Business\Models\Location::TYPE_SELECT as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('location.type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parent Location</label>
                            <select class="form-select" wire:model.defer="location.parent_id">
                                <option value="">None</option>
                                @foreach($allLocations as $parent)
                                    @if(!isset($location['id']) || $location['id'] != $parent->id)
                                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('location.parent_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" wire:model.defer="location.status">
                                @foreach(\Modules\Business\Models\Location::STATUS_SELECT as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('location.status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3" wire:model.defer="location.description"></textarea>
                            @error('location.description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" wire:model.defer="location.is_active">
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeModal">Close</button>
                    @if ($updateMode)
                        <button type="button" class="btn btn-primary" wire:click="update">Save changes</button>
                    @else
                        <button type="button" class="btn btn-primary" wire:click="store">Save</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal modal-blur fade @if($showDeleteModal ?? false) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showDeleteModal ?? false) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-title">Are you sure?</div>
                    <div>Do you really want to delete this location?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="$set('showDeleteModal', false)">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
