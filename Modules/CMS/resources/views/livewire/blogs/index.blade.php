<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Blog Management</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('admin.cms.blogs.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Add New Blog
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
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="flex-grow-1 me-3">
                            <form wire:submit.prevent="filter">
                                <input type="text" class="form-control" wire:model="search"
                                    placeholder="Search blogs...">
                            </form>
                        </div>
                        <div class="flex-shrink-0">
                            {{ $blogs->links() }}
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    @php($currentLocale = app()->getLocale())
                    <table class="table table-vcenter table-hover card-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Title ({{ strtoupper($currentLocale) }})</th>
                                <th>Status</th>
                                <th>Categories</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($blogs as $blog)
                                <tr>
                                    <td>{{ $blog->id }}</td>
                                    <td>{{ $blog->getTranslation('title', $currentLocale) }}</td>
                                    <td>{{ ucfirst($blog->status) }}</td>
                                    <td>
                                        @php($defaultLocale = \Illuminate\Support\Facades\App::getLocale())
                                        @foreach($blog->categories as $category)
                                            <span class="badge bg-secondary">{{ $category->getTranslation('name', $currentLocale) ?? $category->getTranslation('name', $defaultLocale) }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $blog->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.cms.blogs.edit', $blog->id) }}" class="btn btn-sm btn-icon btn-ghost-primary">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex align-items-center">
                    {{ $blogs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
