<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Custom Fields',
        'breadcrumbs' => [['label' => 'Custom Fields', 'active' => true]],
        'actionItems' => [[
            'type' => 'button',
            'title' => 'Add New Field',
            'wireClick' => 'openModal',
            'icon' => 'ti ti-plus',
            'class' => 'btn btn-primary',
        ]],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <input type="text" class="form-control" style="max-width:300px" wire:model.live.debounce.300ms="search" placeholder="Search by Name / Module / Model">
                    @if($fields->hasPages())
                        <div>{{ $fields->links() }}</div>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>ID @include('components.table.sort', ['field' => 'id'])</th>
                                <th>Module @include('components.table.sort', ['field' => 'module'])</th>
                                <th>Model @include('components.table.sort', ['field' => 'model'])</th>
                                <th>Name @include('components.table.sort', ['field' => 'name'])</th>
                                <th>Type @include('components.table.sort', ['field' => 'type'])</th>
                                <th>Required</th>
                                <th>Show in List</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fields as $field)
                                <tr>
                                    <td class="text-secondary">{{ $field->id }}</td>
                                    <td><span class="badge bg-blue-lt">{{ ucfirst($field->module) }}</span></td>
                                    <td class="text-secondary">{{ ucfirst($field->model) }}</td>
                                    <td class="fw-bold">{{ $field->name }}</td>
                                    <td><span class="badge bg-secondary-lt">{{ ucfirst($field->type) }}</span></td>
                                    <td>
                                        <span class="badge {{ $field->is_required ? 'bg-warning-lt' : 'bg-light text-secondary' }}">
                                            {{ $field->is_required ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $field->show_in_list ? 'bg-info-lt' : 'bg-light text-secondary' }}">
                                            {{ $field->show_in_list ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <button wire:click="edit({{ $field->id }})" class="dropdown-item">Edit</button>
                                                <button wire:click="confirmDelete({{ $field->id }})"
                                                        wire:confirm="Are you sure you want to delete this field?"
                                                        class="dropdown-item text-danger">Delete</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-secondary py-4">No custom fields found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($fields->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $fields->links() }}
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
                    <h5 class="modal-title">{{ $updateMode ? 'Edit Field' : 'Add New Field' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Module</label>
                            <select class="form-select" wire:model="module">
                                <option value="">— Select Module —</option>
                                @foreach($modules as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('module')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Model</label>
                            <input type="text" class="form-control" wire:model="model">
                            @error('model')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Field Name</label>
                            <input type="text" class="form-control" wire:model="name">
                            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Field Type</label>
                            <select class="form-select" wire:model="type">
                                <option value="">Select Type</option>
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="select">Select</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="radio">Radio</option>
                                <option value="date">Date</option>
                                <option value="number">Number</option>
                            </select>
                            @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Options (comma separated)</label>
                            <input type="text" class="form-control" wire:model="options">
                            @error('options')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="is_required" wire:model="is_required">
                                <label class="form-check-label" for="is_required">Required</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="show_in_list" wire:model="show_in_list">
                                <label class="form-check-label" for="show_in_list">Show in List</label>
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
                    <div>Do you really want to delete this field?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="$set('showDeleteModal', false)">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
