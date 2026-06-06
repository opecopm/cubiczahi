<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Role List',
        'breadcrumbs' => [['label' => 'Roles', 'active' => true]],
        'actionItems' => [[
            'type' => 'button',
            'title' => 'Add New Role',
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
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Search roles..." />
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
                            @foreach($roles as $role)
                                <tr>
                                    <td class="text-secondary">{{ $role->id }}</td>
                                    <td class="fw-bold">{{ $role->name }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ route('admin.iam.roles.show', $role->id) }}" class="dropdown-item">Manage Permissions</a>
                                                <button wire:click="edit({{ $role->id }})" class="dropdown-item">Edit</button>
                                                <button wire:click="confirmDelete({{ $role->id }})"
                                                        wire:confirm="Are you sure you want to delete this role?"
                                                        class="dropdown-item text-danger">Delete</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($roles->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $roles->links() }}
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
                    <h5 class="modal-title">{{ $updateMode ? 'Edit Role' : 'Add New Role' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Role Name</label>
                            <input type="text" class="form-control" wire:model="name">
                            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeModal">Close</button>
                        <button type="submit" class="btn btn-primary">{{ $updateMode ? 'Save changes' : 'Save' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal modal-blur fade @if($showDeleteModal ?? false) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showDeleteModal ?? false) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-title">Are you sure?</div>
                    <div>Do you really want to delete this role?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="$set('showDeleteModal', false)">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
