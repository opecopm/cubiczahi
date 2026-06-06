<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Workflow Management</h2>
                </div>
                <div class="col-auto ms-auto">
                    <button wire:click="openModal" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                        New Workflow
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <div class="row g-2 w-100">
                        <div class="col">
                            <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search by name, model type, description...">
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
                                @elseif(($meta['type'] ?? null) === 'date')
                                    <input type="date" class="form-control" wire:model.live="filters.{{ $field }}">
                                @else
                                    <input type="text" class="form-control" wire:model.live="filters.{{ $field }}" placeholder="Search {{ ucfirst(str_replace('_',' ',$field)) }}">
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
                                <th>Workflow Name @include('components.table.sort', ['field' => 'name'])</th>
                                <th>Model Type @include('components.table.sort', ['field' => 'model_type'])</th>
                                <th>Context</th>
                                <th>Status @include('components.table.sort', ['field' => 'is_active'])</th>
                                <th>Created</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workflows as $workflow)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $workflow->name }}</div>
                                        <div class="text-secondary small">{{ Str::limit($workflow->description, 50) }}</div>
                                    </td>
                                    <td class="text-secondary small" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                        {{ $workflow->model_type }}
                                    </td>
                                    <td class="text-secondary small">
                                        <div>{{ $workflow->company->name ?? 'Global' }}</div>
                                        @if($workflow->location)<div>{{ $workflow->location->name }}</div>@endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $workflow->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                            {{ $workflow->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-secondary">{{ $workflow->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ route('admin.system.workflows.show', $workflow->id) }}" class="dropdown-item">Configure</a>
                                                <a href="{{ route('admin.system.workflows.show', $workflow->id) }}?tab=instances" class="dropdown-item">Instances</a>
                                                <button wire:click="edit({{ $workflow->id }})" class="dropdown-item">Edit</button>
                                                <button wire:click="confirmDelete({{ $workflow->id }})"
                                                        wire:confirm="Are you sure you want to delete this workflow?"
                                                        class="dropdown-item text-danger">Delete</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-secondary py-4">No workflows found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($workflows->hasPages())
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <small class="text-secondary">Showing {{ $workflows->firstItem() ?? 0 }} to {{ $workflows->lastItem() ?? 0 }} of {{ $workflows->total() }}</small>
                    {{ $workflows->links() }}
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
                    <h5 class="modal-title">{{ $updateMode ? 'Edit Workflow' : 'Add New Workflow' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Workflow Name</label>
                        <input type="text" class="form-control" wire:model="name">
                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Model Type</label>
                        <input type="text" class="form-control" wire:model="model_type" placeholder="e.g. Modules\DMS\Models\Document">
                        @error('model_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Company (Optional)</label>
                            <select class="form-select" wire:model="company_id">
                                <option value="NULL">Global / All</option>
                                @foreach($companies as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location (Optional)</label>
                            <select class="form-select" wire:model="location_id">
                                <option value="NULL">Global / All</option>
                                @foreach($locations as $l)
                                    <option value="{{ $l->id }}">{{ $l->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="3" wire:model="description"></textarea>
                        @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" wire:model="is_active" id="is_active_switch">
                        <label class="form-check-label" for="is_active_switch">Active</label>
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
                    <div>Do you really want to delete this workflow?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="$set('showDeleteModal', false)">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
