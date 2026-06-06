<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Role: ' . $role->name,
        'breadcrumbs' => [
            [
                'label' => 'Roles',
                'url' => route('admin.iam.roles.index'),
                'icon' => 'back',
            ],
            [
                'label' => $role->name,
                'active' => true,
            ],
        ],
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
                    <div class="input-group" style="max-width: 300px;">
                        <input type="text" class="form-control" placeholder="Search permissions..." wire:model.live.debounce.300ms="search">
                    </div>
                    <div class="card-options">
                        <label class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" wire:model.live="selectAllPermissions">
                            <span class="form-check-label">Select All</span>
                        </label>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="savePermissions">
                        @php $currentGroup = null; @endphp
                        @foreach ($permissions as $permission)
                            @if ($currentGroup !== $permission->group_name)
                                @php $currentGroup = $permission->group_name; @endphp
                                @if(!$loop->first)<hr class="my-3">@endif
                                <h4 class="subheader">{{ $currentGroup ?? 'Ungrouped' }}</h4>
                            @endif
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox"
                                       wire:model="selectedPermissions"
                                       value="{{ $permission->name }}"
                                       id="perm-{{ $loop->index }}">
                                <label class="form-check-label" for="perm-{{ $loop->index }}">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        @endforeach

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
