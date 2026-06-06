<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'User List',
        'breadcrumbs' => [['label' => 'Users', 'active' => true]],
        'actionItems' => [[
            'type' => 'button',
            'title' => 'Add New User',
            'wireClick' => 'openModal',
            'icon' => 'ti ti-plus',
            'class' => 'btn btn-primary',
        ]],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            {{-- Stats Row --}}
            <div class="row row-deck row-cards mb-3">
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.status', '')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-blue text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">{{ $usersCount }}</div>
                                    <div class="text-secondary">All Users</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.status', 'active')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-green text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">{{ $activeUsersCount }}</div>
                                    <div class="text-secondary">Active Users</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.status', 'inactive')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-secondary text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12"/><path d="M6 6l12 12"/></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">{{ $inactiveUsersCount }}</div>
                                    <div class="text-secondary">Inactive Users</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.status', 'other')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-red text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M5.7 5.7l12.6 12.6"/></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">{{ $otherUsersCount }}</div>
                                    <div class="text-secondary">Other Users</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="input-group">
                        <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search users...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th style="width:50px">SN</th>
                                <th style="width:60px">Avatar</th>
                                <th>Name @include('components.table.sort', ['field' => 'first_name'])</th>
                                <th>Email @include('components.table.sort', ['field' => 'email'])</th>
                                <th>Status</th>
                                <th>Role</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="user-id-cell text-secondary">
                                        {{ ($users->firstItem() ?? 1) + $loop->index }}
                                        <button type="button" class="btn btn-link p-0 ms-1 copy-user-id" data-user-id="{{ $user->id }}" title="{{ $user->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"/><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"/></svg>
                                        </button>
                                    </td>
                                    <td>
                                        <span class="avatar avatar-sm" style="background-image: url({{ $user->getFirstMediaUrl('avatars') ?: asset('assets/img/no-photo.jpg') }})"></span>
                                    </td>
                                    <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                                    <td class="text-secondary">{{ $user->email }}</td>
                                    <td>
                                        <span class="badge {{ $user->status === 'active' ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                            {{ ucfirst($user->status ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="text-secondary">{{ $user->getRoleNames()->first() ?? '—' }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ route('admin.iam.users.show', $user->id) }}" class="dropdown-item">View</a>
                                                @if(auth()->user()->can('update_users'))
                                                    <button wire:click="edit('{{ $user->id }}')" class="dropdown-item">Edit</button>
                                                @endif
                                                @if(auth()->user()->can('delete_users'))
                                                    <button wire:click="confirmDelete('{{ $user->id }}')" class="dropdown-item text-danger">Delete</button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $users->links() }}
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
                    <h5 class="modal-title">{{ $updateMode ? 'Edit User' : 'Add New User' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" wire:model="first_name">
                                @error('first_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" wire:model="last_name">
                                @error('last_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" wire:model="email">
                                @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" wire:model="password">
                                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <select class="form-select" wire:model="role">
                                    <option value="">Please Select</option>
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->name }}">{{ $r->name }}</option>
                                    @endforeach
                                </select>
                                @error('role')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Menu</label>
                                <select class="form-select" wire:model="menu_id">
                                    <option value="">Please Select</option>
                                    @foreach ($menus as $menu)
                                        <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                                    @endforeach
                                </select>
                                @error('menu_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" wire:model="status">
                                    <option value="">Select Status</option>
                                    @foreach (App\Models\User::STATUS_SELECT as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Avatar</label>
                                <input type="file" class="form-control" wire:model="avatar">
                                @error('avatar')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeModal">Close</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="avatar, store, update">
                            <span wire:loading wire:target="avatar, store, update" class="spinner-border spinner-border-sm me-2"></span>
                            Save & Next
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade @if($deleteId) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0, 0, 0, 0.5);" @if($deleteId) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Confirmation</h5>
                    <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.copy-user-id');
            if (!btn) return;
            var id = btn.getAttribute('data-user-id');
            if (!id) return;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(id);
            } else {
                var el = document.createElement('textarea');
                el.value = id;
                document.body.appendChild(el);
                el.select();
                try { document.execCommand('copy'); } catch (err) {}
                document.body.removeChild(el);
            }
        });
    </script>
    <style>
        .user-id-cell .copy-user-id { opacity: 0; transition: opacity .15s; }
        .user-id-cell:hover .copy-user-id { opacity: 1; }
    </style>
</div>
