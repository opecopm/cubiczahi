<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Project Management</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('admin.cms.projects.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Add New Project
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
                            <form wire:submit.prevent="render">
                                <input type="text" class="form-control" wire:model.live="search"
                                    placeholder="Search projects by title or description...">
                            </form>
                        </div>
                        <div class="flex-shrink-0">{{ $projects->links() }}</div>
                    </div>
                </div>

                <div class="table-responsive">
                    @php($currentLocale = app()->getLocale())
                    <table class="table table-vcenter table-hover card-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Start Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $project)
                                <tr>
                                    <td>{{ $project->project_id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($project->main_image)
                                                <span class="avatar avatar-sm me-2" style="background-image: url({{ asset('storage/' . $project->main_image) }})"></span>
                                            @endif
                                            <div>
                                                <div>{{ $project->getTranslation('project_title', $currentLocale) }}</div>
                                                <div class="text-muted text-sm">{{ Str::limit($project->getTranslation('short_description', $currentLocale), 50) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $project->status === 'completed' ? 'success' : ($project->status === 'in_progress' ? 'info' : 'secondary') }}-lt">
                                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $project->is_active ? 'success' : 'secondary' }}-lt">
                                            {{ $project->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $project->start_date ? $project->start_date->format('d - m - Y') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('admin.cms.projects.edit', $project->project_id) }}" class="btn btn-sm btn-icon btn-ghost-primary">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                        <button wire:click="confirmDelete({{ $project->project_id }})"
                                            class="btn btn-sm btn-icon btn-ghost-danger"
                                            wire:confirm="Are you sure you want to delete this project?">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center p-4 text-muted">No projects found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex align-items-center">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
