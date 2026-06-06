<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Taxes',
        'breadcrumbs' => [['label' => 'Taxes', 'active' => true]],
        'actionItems' => [[
            'type' => 'button',
            'title' => 'Add New Tax',
            'wireClick' => 'openModal',
            'icon' => 'ti ti-plus',
            'class' => 'btn btn-primary',
        ]],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
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
                                <th># @include('components.table.sort', ['field' => 'id'])</th>
                                <th>Name @include('components.table.sort', ['field' => 'name'])</th>
                                <th>Rate (%) @include('components.table.sort', ['field' => 'rate'])</th>
                                <th>Company</th>
                                <th>Status @include('components.table.sort', ['field' => 'status'])</th>
                                <th>Default @include('components.table.sort', ['field' => 'is_default'])</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($taxes as $tax)
                                <tr>
                                    <td class="text-secondary">{{ $tax->id }}</td>
                                    <td class="fw-bold">{{ $tax->name }}</td>
                                    <td>{{ number_format($tax->rate, 2) }}%</td>
                                    <td class="text-secondary">{{ $tax->company->getTranslation('name', 'en') ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $tax->status === 'active' ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                            {{ ucfirst($tax->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $tax->is_default ? 'bg-info-lt' : 'bg-light text-secondary' }}">
                                            {{ $tax->is_default ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <button wire:click="edit({{ $tax->id }})" class="dropdown-item">Edit</button>
                                                <button wire:click="confirmDelete({{ $tax->id }})"
                                                        wire:confirm="Are you sure you want to delete this tax?"
                                                        class="dropdown-item text-danger">Delete</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-secondary py-4">No taxes found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($taxes->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $taxes->links() }}
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
                    <h5 class="modal-title">{{ $updateMode ? 'Edit Tax' : 'Add Tax' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" wire:model.defer="name">
                            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rate (%)</label>
                            <input type="number" step="0.01" class="form-control" wire:model.defer="rate">
                            @error('rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" wire:model.defer="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company</label>
                            <select class="form-select" wire:model.defer="company_id">
                                <option value="">— Select Company —</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->getTranslation('name', 'en') }}</option>
                                @endforeach
                            </select>
                            @error('company_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_default" wire:model.defer="is_default">
                                <label class="form-check-label" for="is_default">Default</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeModal">Cancel</button>
                    @if ($updateMode)
                        <button type="button" class="btn btn-primary" wire:click="update">Update</button>
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
                    <div>Do you really want to delete this tax?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="$set('showDeleteModal', false)">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
