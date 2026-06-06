<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Permission Groups',
        'breadcrumbs' => [['label' => 'Permission Groups', 'active' => true]],
        'actionItems' => [
            [
                'title' => 'Permissions',
                'route' => 'admin.iam.permissions.index',
                'class' => 'btn btn-outline-secondary',
            ],
            [
                'title' => 'Add Group',
                'route' => 'admin.iam.permission-groups.create',
                'icon' => 'ti ti-plus',
                'class' => 'btn btn-primary',
            ],
        ],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Search groups..." />
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Permissions</th>
                                <th>Created</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groups as $group)
                            <tr>
                                <td class="fw-bold">{{ $group->name }}</td>
                                <td class="text-secondary">{{ $group->description ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-blue-lt text-blue">{{ $group->permissions_count }} permissions</span>
                                </td>
                                <td class="text-secondary">{{ $group->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{ route('admin.iam.permissions.groups.edit', $group) }}" class="dropdown-item">Edit</a>
                                            <button wire:click="deleteGroup({{ $group->id }})"
                                                    wire:confirm="Are you sure you want to delete this group?"
                                                    class="dropdown-item text-danger">Delete</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">No permission groups found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($groups->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $groups->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
