<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Menu Item Management</h2>
                </div>
                <div class="col-auto ms-auto">
                    <button wire:click="openModal" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                        Add New Menu Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <form wire:submit="filter" class="d-flex gap-2">
                        <input type="text" class="form-control" wire:model="search" placeholder="Search menu items...">
                    </form>
                    @if($menuItems->hasPages())
                        <div>{{ $menuItems->links() }}</div>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Title @include('components.table.sort', ['field' => 'title'])</th>
                                <th>Prefix</th>
                                <th>URL</th>
                                <th>Icon</th>
                                <th>Order @include('components.table.sort', ['field' => 'order'])</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menuItems as $index => $menuItem)
                                <tr>
                                    <td class="text-secondary">{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $menuItem->title }}</td>
                                    <td class="text-secondary">{{ $menuItem->prefix }}</td>
                                    <td class="text-secondary">{{ $menuItem->url }}</td>
                                    <td class="text-secondary">{{ $menuItem->icon }}</td>
                                    <td>{{ $menuItem->order }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <button wire:click="edit({{ $menuItem->id }})" class="dropdown-item">Edit</button>
                                                <button wire:click="confirmDelete({{ $menuItem->id }})"
                                                        wire:confirm="Are you sure you want to delete this menu item?"
                                                        class="dropdown-item text-danger">Delete</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @foreach($menuItem->children as $index2 => $childItem)
                                    <tr class="table-active">
                                        <td class="text-secondary ps-3">{{ ($index + 1) }}.{{ ($index2 + 1) }}</td>
                                        <td class="ps-4 text-secondary">{{ $childItem->title }}</td>
                                        <td class="text-secondary">{{ $childItem->prefix }}</td>
                                        <td class="text-secondary">{{ $childItem->url }}</td>
                                        <td class="text-secondary">{{ $childItem->icon }}</td>
                                        <td>{{ $childItem->order }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <button wire:click="edit({{ $childItem->id }})" class="dropdown-item">Edit</button>
                                                    <button wire:click="confirmDelete({{ $childItem->id }})"
                                                            wire:confirm="Are you sure you want to delete this menu item?"
                                                            class="dropdown-item text-danger">Delete</button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @foreach($childItem->children as $index3 => $grandChildItem)
                                        <tr>
                                            <td class="text-secondary ps-3">{{ ($index + 1) }}.{{ ($index2 + 1) }}.{{ ($index3 + 1) }}</td>
                                            <td class="ps-5 text-secondary">{{ $grandChildItem->title }}</td>
                                            <td class="text-secondary">{{ $grandChildItem->prefix }}</td>
                                            <td class="text-secondary">{{ $grandChildItem->url }}</td>
                                            <td class="text-secondary">{{ $grandChildItem->icon }}</td>
                                            <td>{{ $grandChildItem->order }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <button wire:click="edit({{ $grandChildItem->id }})" class="dropdown-item">Edit</button>
                                                        <button wire:click="confirmDelete({{ $grandChildItem->id }})"
                                                                wire:confirm="Are you sure you want to delete this menu item?"
                                                                class="dropdown-item text-danger">Delete</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($menuItems->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $menuItems->links() }}
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
                    <h5 class="modal-title">{{ $updateMode ? 'Update' : 'Create' }} Menu Item</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" wire:model="title">
                            @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prefix</label>
                            <input type="text" class="form-control" wire:model="prefix">
                            @error('prefix')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">URL</label>
                            <input type="text" class="form-control" wire:model="url">
                            @error('url')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Icon</label>
                            <input type="text" class="form-control" wire:model="icon">
                            @error('icon')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Order</label>
                            <input type="number" class="form-control" wire:model="order">
                            @error('order')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Parent Menu Item</label>
                            <select class="form-select" wire:model="parentId">
                                <option value="">Select Parent Menu Item</option>
                                @foreach($allMenuItems as $item)
                                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                                @endforeach
                            </select>
                            @error('parentId')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
                    <div>Do you really want to delete this menu item?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="$set('showDeleteModal', false)">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
