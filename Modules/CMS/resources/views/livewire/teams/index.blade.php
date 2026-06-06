<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Team Members</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('admin.cms.teams.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Add Member
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
                                    placeholder="Search team members...">
                            </form>
                        </div>
                        <div class="flex-shrink-0">
                            {{ $teams->links() }}
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-vcenter table-hover card-table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Phone</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teams as $index => $team)
                                <tr>
                                    <td>{{ $teams->firstItem() + $index }}</td>
                                    <td>
                                        @if($team->photo && \Storage::disk('public')->exists($team->photo))
                                            <img src="{{ asset('storage/' . $team->photo) }}" width="40" height="40"
                                                class="rounded-circle">
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td>{{ $team->getTranslation('name', app()->getLocale()) ?? '-' }}</td>
                                    <td>{{ $team->getTranslation('designation', app()->getLocale()) ?? '-' }}</td>
                                    <td>{{ $team->getTranslation('phone', app()->getLocale()) ?? '-' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($team->getTranslation('message', app()->getLocale()), 40) ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $team->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $team->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $team->created_at->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.cms.teams.edit', $team->id) }}"
                                            class="btn btn-sm btn-icon btn-ghost-primary">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                        <button wire:click="delete({{ $team->id }})"
                                            class="btn btn-sm btn-icon btn-ghost-danger"
                                            wire:confirm="Are you sure you want to delete this team member?">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No team members found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex align-items-center">
                    {{ $teams->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
