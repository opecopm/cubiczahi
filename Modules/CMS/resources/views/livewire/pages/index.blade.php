<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Page Management</h2>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-list">
                        <a href="{{ route('admin.cms.pages.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i> Add New Page
                        </a>
                        <a href="{{ route('admin.cms.page-builder.create') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-tools me-1"></i> Create Page Builder
                        </a>
                        <a href="{{ route('admin.cms.page-builder.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-layout-dashboard me-1"></i> Page Builder Manager
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">

            {{-- Stat Cards --}}
            <div class="row row-deck row-cards mb-3">
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer;border-top:3px solid var(--tblr-blue)"
                         wire:click="$set('statusFilter', '')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar rounded bg-blue-lt text-blue">
                                        <i class="ti ti-file-text fs-3"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="fw-bold fs-3">{{ $totalCount }}</div>
                                    <div class="text-secondary small">All Pages</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer;border-top:3px solid var(--tblr-green)"
                         wire:click="$set('statusFilter', 'published')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar rounded bg-green-lt text-green">
                                        <i class="ti ti-circle-check fs-3"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="fw-bold fs-3">{{ $publishedCount }}</div>
                                    <div class="text-secondary small">Published</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer;border-top:3px solid var(--tblr-secondary)"
                         wire:click="$set('statusFilter', 'draft')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar rounded bg-secondary-lt text-secondary">
                                        <i class="ti ti-file fs-3"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="fw-bold fs-3">{{ $draftCount }}</div>
                                    <div class="text-secondary small">Draft</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('components.tabler.alerts')

            <div class="card">
                {{-- Filters --}}
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
                            <button wire:click="$set('search', '')" class="btn btn-outline-secondary" title="Reset filters">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover card-table">
                        <thead class="table-light">
                            <tr>
                                <th role="button" wire:click="sortBy('title')" class="cursor-pointer">
                                    <span class="d-flex align-items-center gap-1">
                                        Title
                                        @if ($sortBy === 'title')
                                            <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                        @endif
                                    </span>
                                </th>
                                <th role="button" wire:click="sortBy('slug')" class="cursor-pointer">
                                    <span class="d-flex align-items-center gap-1">
                                        Slug
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
                                <th>Template</th>
                                <th>Published At</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pages as $page)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            <a href="{{ route('admin.cms.pages.show', $page->id) }}"
                                               class="text-reset text-decoration-none">
                                                {{ $page->title }}
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-muted">{{ $page->slug }}</code>
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
                                    <td>
                                        @if($page->template_type === 'page_builder')
                                            <span class="badge bg-blue-lt text-blue">Page Builder</span>
                                        @elseif($page->template_type === 'custom')
                                            <span class="badge bg-purple-lt text-purple">{{ $page->template_name }}</span>
                                        @else
                                            <span class="badge bg-secondary-lt text-secondary">Default</span>
                                        @endif
                                    </td>
                                    <td class="text-secondary">
                                        {{ $page->published_at ? \Carbon\Carbon::parse($page->published_at)->format('d M Y') : '—' }}
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('admin.cms.pages.show', $page->id) }}"
                                               class="btn btn-sm btn-icon btn-ghost-secondary" title="View">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.cms.pages.edit', $page->id) }}"
                                               class="btn btn-sm btn-icon btn-ghost-primary" title="Edit">
                                                <i class="ti ti-pencil"></i>
                                            </a>
                                            @if($page->template_type === 'page_builder')
                                                <a href="{{ route('admin.cms.page-builder.builder', $page->id) }}"
                                                   class="btn btn-sm btn-icon btn-ghost-cyan" title="Page Builder">
                                                    <i class="ti ti-tools"></i>
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-icon btn-ghost-secondary"
                                                        title="Save with Page Builder template first" disabled>
                                                    <i class="ti ti-tools" style="opacity:.4;"></i>
                                                </button>
                                            @endif
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
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty">
                                            <div class="empty-img">
                                                <i class="ti ti-file-off" style="font-size:3rem;color:var(--tblr-secondary)"></i>
                                            </div>
                                            <p class="empty-title">No pages found</p>
                                            <p class="empty-subtitle text-secondary">
                                                Try adjusting your search or filters.
                                            </p>
                                            <div class="empty-action">
                                                <a href="{{ route('admin.cms.pages.create') }}" class="btn btn-primary">
                                                    <i class="ti ti-plus me-1"></i> Add first page
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
