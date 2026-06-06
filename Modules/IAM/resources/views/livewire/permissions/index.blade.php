<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Permission List',
        'breadcrumbs' => [['label' => 'Permissions', 'active' => true]],
        'actionItems' => [[
            'type' => 'button',
            'title' => 'Add New Permission',
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
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search permissions...">
                </div>
                <div class="card-body">
                    @php
                        $groupedPermissions = collect($permissions->items() ?? $permissions)->groupBy('group_name');
                    @endphp

                    @foreach($groupedPermissions as $groupName => $group)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center bg-light rounded px-3 py-2 mb-2">
                                <h5 class="mb-0">
                                    <strong>{{ $groupName ?: 'Unassigned' }}</strong>
                                </h5>
                                <button class="btn btn-sm btn-ghost-secondary" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#permission-group-{{ $loop->index }}">
                                    Toggle
                                </button>
                            </div>
                            <div id="permission-group-{{ $loop->index }}" class="collapse @if($loop->index == 0) show @endif">
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th style="width:60px">ID @include('components.table.sort', ['field' => 'permissions.id'])</th>
                                                <th>Name @include('components.table.sort', ['field' => 'permissions.name'])</th>
                                                <th class="w-1"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($group as $permission)
                                                <tr>
                                                    <td class="text-secondary">{{ $permission->id }}</td>
                                                    <td>{{ $permission->name }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button wire:click="edit({{ $permission->id }})" class="btn btn-sm btn-ghost-secondary">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/></svg>
                                                            </button>
                                                            <button wire:click="confirmDelete({{ $permission->id }})"
                                                                    wire:confirm="Are you sure you want to delete this permission?"
                                                                    class="btn btn-sm btn-ghost-danger">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($permissions->hasPages())
                        <div class="mt-3">{{ $permissions->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal modal-blur fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $updateMode ? 'Edit Permission' : 'Add New Permission' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Permission Name</label>
                        <input type="text" class="form-control" wire:model="name">
                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permission Group</label>
                        <select class="form-select" wire:model="permissionGroupId">
                            <option value="">Select Permission Group</option>
                            @foreach($permissionGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                        @error('permissionGroupId')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
                    <div>Do you really want to delete this permission?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="$set('showDeleteModal', false)">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
