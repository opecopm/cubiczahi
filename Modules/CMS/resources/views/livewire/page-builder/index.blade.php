<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Page Builder Management</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('admin.cms.page-builder.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Create New Page
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @include('components.tabler.alerts')

            <div class="card">
                <div class="card-header border-bottom">
                    <div class="row g-2 w-100 align-items-center">
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control"
                                       wire:model.live.debounce.300ms="search"
                                       placeholder="Search by title or slug…">
                            </div>
                        </div>
                        <div class="col-auto">
                            <button wire:click="$set('search', '')" class="btn btn-outline-secondary" title="Reset">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-vcenter table-hover card-table">
                        <thead class="table-light">
                            <tr>
                                <th role="button" wire:click="sortBy('slug')" class="cursor-pointer">
                                    <span class="d-flex align-items-center gap-1">
                                        Title / Slug
                                        @if ($sortBy === 'slug')
                                            <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                        @endif
                                    </span>
                                </th>
                                <th role="button" wire:click="sortBy('status')" class="cursor-pointer">
                                    <span class="d-flex align-items-center gap-1">
                                        Status
                                        @if ($sortBy === 'status')
                                            <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                        @endif
                                    </span>
                                </th>
                                <th>Published At</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pages as $page)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $page->getTranslation('title', app()->getLocale(), false) ?: $page->slug }}</div>
                                        <code class="text-muted small">{{ $page->slug }}</code>
                                    </td>
                                    <td>
                                        @if($page->status === 'published')
                                            <span class="badge bg-success-lt text-success d-inline-flex align-items-center gap-1">
                                                <span class="badge-dot bg-success"></span> Published
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-lt text-secondary d-inline-flex align-items-center gap-1">
                                                <span class="badge-dot bg-secondary"></span> Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-secondary">
                                        {{ $page->published_at ? $page->published_at->format('d M Y') : '—' }}
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('admin.cms.page-builder.show', $page->id) }}"
                                               class="btn btn-sm btn-icon btn-ghost-secondary" title="View">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.cms.page-builder.builder', $page->id) }}"
                                               class="btn btn-sm btn-icon btn-ghost-cyan" title="Open Builder">
                                                <i class="ti ti-tools"></i>
                                            </a>
                                            <button wire:click="duplicate({{ $page->id }})"
                                                    class="btn btn-sm btn-icon btn-ghost-warning" title="Duplicate">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                            <button wire:click="confirmDelete({{ $page->id }})"
                                                    wire:confirm="Are you sure you want to delete this page?"
                                                    class="btn btn-sm btn-icon btn-ghost-danger" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="empty">
                                            <div class="empty-img">
                                                <i class="ti ti-layout-off" style="font-size:3rem;color:var(--tblr-secondary)"></i>
                                            </div>
                                            <p class="empty-title">No pages found</p>
                                            <p class="empty-subtitle text-secondary">Try adjusting your search or create a new page.</p>
                                            <div class="empty-action">
                                                <a href="{{ route('admin.cms.page-builder.create') }}" class="btn btn-primary">
                                                    <i class="ti ti-plus me-1"></i> Create New Page
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pages->hasPages())
                    <div class="card-footer d-flex align-items-center justify-content-between py-3">
                        <small class="text-secondary">
                            Showing {{ $pages->firstItem() ?? 0 }}–{{ $pages->lastItem() ?? 0 }}
                            of <strong>{{ $pages->total() }}</strong>
                        </small>
                        {{ $pages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
