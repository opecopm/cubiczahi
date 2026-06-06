<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Testimonials Management</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('admin.cms.testimonials.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Add New Testimonial
                    </a>
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
                                        <i class="ti ti-message-share fs-3"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="fw-bold fs-3">{{ $totalCount }}</div>
                                    <div class="text-secondary small">All Testimonials</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer;border-top:3px solid var(--tblr-green)"
                         wire:click="$set('statusFilter', 'active')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar rounded bg-green-lt text-green">
                                        <i class="ti ti-circle-check fs-3"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="fw-bold fs-3">{{ $activeCount }}</div>
                                    <div class="text-secondary small">Active</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer;border-top:3px solid var(--tblr-secondary)"
                         wire:click="$set('statusFilter', 'inactive')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar rounded bg-secondary-lt text-secondary">
                                        <i class="ti ti-eye-off fs-3"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="fw-bold fs-3">{{ $inactiveCount }}</div>
                                    <div class="text-secondary small">Inactive</div>
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
                                       placeholder="Search by name, designation, company or email…">
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
                                <th role="button" wire:click="sortBy('id')" class="cursor-pointer">
                                    <span class="d-flex align-items-center gap-1">
                                        ID
                                        @if ($sortBy === 'id')
                                            <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                        @endif
                                    </span>
                                </th>
                                <th>Image</th>
                                <th role="button" wire:click="sortBy('name')" class="cursor-pointer">
                                    <span class="d-flex align-items-center gap-1">
                                        Name
                                        @if ($sortBy === 'name')
                                            <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                        @endif
                                    </span>
                                </th>
                                <th role="button" wire:click="sortBy('designation')" class="cursor-pointer">
                                    <span class="d-flex align-items-center gap-1">
                                        Designation
                                        @if ($sortBy === 'designation')
                                            <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                        @endif
                                    </span>
                                </th>
                                <th role="button" wire:click="sortBy('rating')" class="cursor-pointer">
                                    <span class="d-flex align-items-center gap-1">
                                        Rating
                                        @if ($sortBy === 'rating')
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
                                <th class="w-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($testimonials as $testimonial)
                                <tr>
                                    <td>
                                        <span class="text-secondary">#{{ $testimonial->id }}</span>
                                    </td>
                                    <td>
                                        @if($testimonial->image)
                                            <span class="avatar avatar-sm rounded-circle" style="background-image: url({{ asset('storage/'.$testimonial->image) }})"></span>
                                        @else
                                            <span class="avatar avatar-sm rounded-circle bg-secondary-lt text-secondary fw-bold">
                                                {{ strtoupper(substr($testimonial->getTranslation('name', 'en') ?? 'T', 0, 1)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $testimonial->getTranslation('name', 'en') ?? '—' }}
                                        </div>
                                        @if($testimonial->email)
                                            <div class="text-secondary small">{{ $testimonial->email }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $testimonial->getTranslation('designation', 'en') ?? '—' }}</div>
                                        @if($testimonial->getTranslation('company', 'en'))
                                            <div class="text-secondary small">{{ $testimonial->getTranslation('company', 'en') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($testimonial->rating)
                                            <div class="d-flex text-warning gap-0.5" title="{{ $testimonial->rating }} Stars">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $testimonial->rating)
                                                        {{-- Filled Star SVG --}}
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star text-warning" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="currentColor" stroke-linecap="round" stroke-linejoin="round" style="width: 1.1rem; height: 1.1rem;">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                                        </svg>
                                                    @else
                                                        {{-- Outline Star SVG --}}
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star text-warning" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" style="width: 1.1rem; height: 1.1rem; opacity: 0.35;">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                                        </svg>
                                                    @endif
                                                @endfor
                                            </div>
                                        @else
                                            <span class="text-secondary">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($testimonial->status)
                                            <span class="badge bg-success-lt text-success d-inline-flex align-items-center gap-1">
                                                <span class="badge-dot bg-success"></span> Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-lt text-secondary d-inline-flex align-items-center gap-1">
                                                <span class="badge-dot bg-secondary"></span> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('admin.cms.testimonials.edit', $testimonial->id) }}"
                                               class="btn btn-sm btn-icon btn-ghost-primary" title="Edit">
                                                <i class="ti ti-pencil"></i>
                                            </a>
                                            <button wire:click="delete({{ $testimonial->id }})"
                                                    wire:confirm="Are you sure you want to delete this testimonial?"
                                                    class="btn btn-sm btn-icon btn-ghost-danger" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty">
                                            <div class="empty-img">
                                                <i class="ti ti-message-off" style="font-size:3rem;color:var(--tblr-secondary)"></i>
                                            </div>
                                            <p class="empty-title">No testimonials found</p>
                                            <p class="empty-subtitle text-secondary">
                                                Try adjusting your search or filters.
                                            </p>
                                            <div class="empty-action">
                                                <a href="{{ route('admin.cms.testimonials.create') }}" class="btn btn-primary">
                                                    <i class="ti ti-plus me-1"></i> Add first testimonial
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($testimonials->hasPages())
                    <div class="card-footer d-flex align-items-center justify-content-between py-3">
                        <small class="text-secondary">
                            Showing {{ $testimonials->firstItem() ?? 0 }}–{{ $testimonials->lastItem() ?? 0 }}
                            of <strong>{{ $testimonials->total() }}</strong>
                        </small>
                        {{ $testimonials->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
