<div>
    @component('admin.partials.page.inner-header', [
        'title' => $team->name,
        'breadcrumbs' => [
            [
                'label' => 'Teams',
                'url' => route('admin.iam.teams.index'),
                'icon' => 'back',
            ],
            [
                'label' => $team->name,
                'active' => true,
            ],
        ],
        'actionItems' => [[
            'type' => 'button',
            'title' => 'Add New Member',
            'wireClick' => 'openModal',
            'icon' => 'ti ti-plus',
            'class' => 'btn btn-primary',
        ]],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible mb-3">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search members...">
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                                <tr>
                                    <td class="text-secondary">{{ $member->id }}</td>
                                    <td>{{ $member->user->first_name }} {{ $member->user->last_name }}</td>
                                    <td class="text-secondary">{{ $member->user->email }}</td>
                                    <td><span class="badge bg-blue-lt">{{ ucfirst($member->role) }}</span></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <button wire:click="editMember('{{ $member->id }}')" class="dropdown-item">Edit</button>
                                                <button wire:click="confirmDelete('{{ $member->id }}')"
                                                        wire:confirm="Are you sure you want to remove this member?"
                                                        class="dropdown-item text-danger">Remove</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($members->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $members->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add/Edit Member Modal --}}
    <div class="modal modal-blur fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditing ? 'Edit Member' : 'Add New Member' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="storeMember">
                    <div class="modal-body">
                        @if(!$isEditing)
                            <div class="mb-3 position-relative">
                                <label class="form-label">Search User</label>
                                <input type="text" class="form-control" wire:model.live="searchUser" placeholder="Type to search...">
                                @if(!empty($searchResults))
                                    <ul class="list-group mt-1 position-absolute w-100" style="z-index: 1050; max-height: 200px; overflow-y: auto;">
                                        @foreach($searchResults as $result)
                                            <li class="list-group-item list-group-item-action" style="cursor:pointer" wire:click="selectUser('{{ $result->id }}')" wire:key="user-{{ $result->id }}">
                                                {{ $result->first_name }} {{ $result->last_name }} ({{ $result->email }})
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if($userId)
                                    <div class="mt-2 text-success small">Selected: {{ $searchUser }}</div>
                                @endif
                                @error('userId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        @else
                            <div class="mb-3">
                                <label class="form-label">User</label>
                                <p class="form-control-plaintext fw-bold">{{ $searchUser }}</p>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" wire:model="role">
                                <option value="member">Member</option>
                                <option value="leader">Leader</option>
                                <option value="admin">Admin</option>
                            </select>
                            @error('role') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeModal">Close</button>
                        <button type="submit" class="btn btn-primary">{{ $isEditing ? 'Update' : 'Add Member' }}</button>
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
                    <div>Do you really want to remove this member?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="$set('showDeleteModal', false)">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Yes, remove</button>
                </div>
            </div>
        </div>
    </div>
</div>
