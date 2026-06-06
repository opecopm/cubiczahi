<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Reference Schemas',
        'breadcrumbs' => [['label' => 'Reference Schemas', 'active' => true]],
        'actionItems' => [[
            'type' => 'button',
            'title' => 'Add New Schema',
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
                    <input type="text" class="form-control" style="max-width:300px" wire:model.live.debounce.300ms="search" placeholder="Search by Type...">
                    @if($schemas->hasPages())
                        <div>{{ $schemas->links() }}</div>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>ID @include('components.table.sort', ['field' => 'id'])</th>
                                <th>Type @include('components.table.sort', ['field' => 'type'])</th>
                                <th>Prefix</th>
                                <th>Model</th>
                                <th>Date Prefix</th>
                                <th>Reset Period</th>
                                <th>Initial</th>
                                <th>Increment</th>
                                <th>Next</th>
                                <th>Digits</th>
                                <th>Status @include('components.table.sort', ['field' => 'status'])</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schemas as $schema)
                                <tr>
                                    <td class="text-secondary">{{ $schema->id }}</td>
                                    <td class="fw-bold">{{ $schema->type }}</td>
                                    <td>{{ $schema->prefix }}</td>
                                    <td class="text-secondary small">{{ $schema->model }}</td>
                                    <td>{{ $schema->date_prefix }}</td>
                                    <td><span class="badge bg-secondary-lt">{{ $schema->reset_period ?? 'none' }}</span></td>
                                    <td>{{ $schema->initial_value }}</td>
                                    <td>{{ $schema->increment }}</td>
                                    <td>{{ $schema->next_value }}</td>
                                    <td>{{ $schema->digits }}</td>
                                    <td>
                                        <span class="badge {{ $schema->status === 'active' ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                            {{ ucfirst($schema->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <button wire:click="edit({{ $schema->id }})" class="dropdown-item">Edit</button>
                                                <button wire:click="confirmDelete({{ $schema->id }})"
                                                        wire:confirm="Are you sure you want to delete this schema?"
                                                        class="dropdown-item text-danger">Delete</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="12" class="text-center text-secondary py-4">No reference schemas found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($schemas->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $schemas->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal modal-blur fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $updateMode ? 'Edit Schema' : 'Add New Schema' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <input type="text" class="form-control" wire:model="type">
                            @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prefix</label>
                            <input type="text" class="form-control" wire:model="prefix">
                            @error('prefix')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Model Class</label>
                            <input type="text" class="form-control" wire:model="model" placeholder="Modules\SupportDesk\Models\SupportTicket">
                            @error('model')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date Prefix (PHP format, e.g. Ymd)</label>
                            <input type="text" class="form-control" wire:model="date_prefix">
                            @error('date_prefix')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Initial Value</label>
                            <input type="number" class="form-control" wire:model="initial_value">
                            @error('initial_value')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Increment</label>
                            <input type="number" class="form-control" wire:model="increment">
                            @error('increment')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Next Value</label>
                            <input type="number" class="form-control" wire:model="next_value">
                            @error('next_value')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Digits</label>
                            <input type="number" class="form-control" wire:model="digits">
                            @error('digits')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reset Period</label>
                            <select class="form-select" wire:model="reset_period">
                                <option value="none">None</option>
                                <option value="daily">Daily</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                            @error('reset_period')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" wire:model="status">
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
                    <div>Do you really want to delete this schema?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="$set('showDeleteModal', false)">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
