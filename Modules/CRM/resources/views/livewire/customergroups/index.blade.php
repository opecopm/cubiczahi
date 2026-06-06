<div>
    @component('admin.partials.page.inner-header', [
        'title' => 'Customer Group Management',
        'breadcrumbs' => [
            [
                'label' => 'Customer Groups',
                'active' => true,
            ],
        ],
        'actionItems' => [
            [
                'type' => 'button',
                'title' => 'Add New Group',
                'wireClick' => 'openModal',
                'icon' => 'ti ti-plus',
                'class' => 'btn btn-primary',
            ],
        ],
    ])
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            @if (session()->has('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="row g-2 w-100">
                        <div class="col">
                            <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search by name">
                        </div>
                        <div class="col-auto">
                            {{ $groups->links() }}
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>ID @include('components.table.sort', ['field' => 'id'])</th>
                                <th>Name @include('components.table.sort', ['field' => 'name'])</th>
                                <th>Parent Group</th>
                                <th class="w-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groups->where('parent_id', null) as $group)
                                @include('crm::livewire.customergroups.partials.group-row', ['group' => $group, 'level' => 0])
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" @if($showModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $updateMode ? 'Edit Group' : 'Add New Group' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" wire:model.defer="name">
                                @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Parent Group</label>
                                <select class="form-select" wire:model.defer="parent_id">
                                    <option value="">-- None --</option>
                                    @php
                                        $renderParentOptions = function ($groups, $prefix = '', $excludeId = null) use (&$renderParentOptions) {
                                            foreach ($groups as $grp) {
                                                if ($excludeId !== $grp->id) {
                                                    echo '<option value="' . $grp->id . '">' . $prefix . e($grp->name) . '</option>';
                                                }
                                                if ($grp->children && $grp->children->count()) {
                                                    $renderParentOptions($grp->children, $prefix . '— ', $excludeId);
                                                }
                                            }
                                        };
                                        $renderParentOptions($parents, '', $updateMode ? $groupId : null);
                                    @endphp
                                </select>
                                @error('parent_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeModal">Close</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="store,update">
                            <span wire:loading wire:target="store,update" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            {{ $updateMode ? 'Save changes' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin.livewire.partials.delete-confirmation-modal')
</div>
