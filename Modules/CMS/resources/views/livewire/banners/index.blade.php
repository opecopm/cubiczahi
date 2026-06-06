<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Banner Management</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <button type="button" wire:click="createNewBanner" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Add New Banner
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @include('components.tabler.alerts')

            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="flex-grow-1 me-3">
                            <form wire:submit.prevent="filter">
                                <input type="text" class="form-control" wire:model="search"
                                    placeholder="Search banners...">
                            </form>
                        </div>
                        <div class="flex-shrink-0">
                            {{ $banners->links() }}
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-vcenter table-hover card-table">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Items Count</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($banners as $index => $banner)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $banner->getTranslation('name', 'en') ?? 'No Name' }}</td>
                                    <td>{{ $banner->slug }}</td>
                                    <td class="text-center">
                                        @if($banner->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $banner->items->count() }}</td>
                                     <td class="text-center">
                                         <button wire:click="editBanner({{ $banner->id }})"
                                             class="btn btn-sm btn-icon btn-ghost-warning" title="Quick Edit">
                                             <i class="ti ti-bolt"></i>
                                         </button>
                                         <a href="{{ route('admin.cms.banners.edit', $banner->id) }}"
                                             class="btn btn-sm btn-icon btn-ghost-primary" title="Full Edit">
                                             <i class="ti ti-pencil"></i>
                                         </a>
                                        <form action="{{ route('admin.cms.banners.destroy', $banner->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-ghost-danger"
                                                onclick="return confirm('Are you sure you want to delete this banner?')">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No banners found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex align-items-center">
                    {{ $banners->links() }}
                </div>
            </div>
        </div>
    </div>
    @include('cms::livewire.partials.quick-edit-banner-modal')
</div>
