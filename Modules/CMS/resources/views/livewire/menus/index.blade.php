<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Menu Management</h2>
                </div>
                <div class="col-auto ms-auto d-flex align-items-center gap-2">
                    {{-- Language Switcher --}}
                    <select class="form-select border-secondary text-secondary fw-semibold"
                        wire:model.live="activeLocale"
                        style="width: auto; height: 36px; padding-top: 4px; padding-bottom: 4px;">
                        @foreach ($activeLanguages as $lang)
                            <option value="{{ $lang->code }}">
                                🌐 {{ $lang->name }} ({{ strtoupper($lang->code) }})
                            </option>
                        @endforeach
                    </select>
                    <button wire:click="openModal" class="btn btn-primary" style="height:36px;">
                        <i class="ti ti-plus me-1"></i> Add New Menu
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
                            <form wire:submit="filter">
                                <input type="text" class="form-control" wire:model="search" placeholder="Search menus...">
                            </form>
                        </div>
                        <div class="flex-shrink-0">{{ $menus->links() }}</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-vcenter table-hover card-table">
                        <thead>
                            <tr>
                                <th>ID @include('components.table.sort', ['field' => 'id'])</th>
                                <th>Name ({{ strtoupper($activeLocale) }})</th>
                                <th>Slug</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menus as $menu)
                                <tr>
                                    <td>{{ $menu->id }}</td>
                                    <td>
                                        <strong>{{ $menu->getTranslation('name', $activeLocale, false) ?: $menu->getTranslation('name', 'en', false) }}</strong>
                                    </td>
                                    <td><code class="small text-muted">{{ $menu->slug }}</code></td>
                                    <td>
                                        <a href="{{ route('admin.cms.menus.show', $menu->id) }}"
                                            class="btn btn-sm btn-icon btn-ghost-secondary" title="Manage Items">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <button wire:click="edit({{ $menu->id }})"
                                            class="btn btn-sm btn-icon btn-ghost-primary" title="Edit">
                                            <i class="ti ti-pencil"></i>
                                        </button>
                                        <button wire:click="confirmDelete({{ $menu->id }})"
                                            class="btn btn-sm btn-icon btn-ghost-danger"
                                            wire:confirm="Are you sure you want to delete this menu?"
                                            title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex align-items-center">
                    {{ $menus->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Create / Edit Modal --}}
    <div class="modal modal-blur fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog"
        style="@if($showModal) background: rgba(0, 0, 0, 0.5); @endif"
        @if($showModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $updateMode ? 'Edit Menu' : 'Add New Menu' }}</h5>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        {{-- Language Switcher inside modal --}}
                        <select class="form-select form-select-sm border-secondary"
                            wire:model.live="activeLocale" style="width: auto;">
                            @foreach ($activeLanguages as $lang)
                                <option value="{{ $lang->code }}">
                                    🌐 {{ $lang->name }} ({{ strtoupper($lang->code) }})
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                </div>
                <div class="modal-body">
                    @php
                        $activeDir = $activeLanguages->where('code', $activeLocale)->first()?->direction ?? 'ltr';
                    @endphp
                    <form>
                        {{-- Translatable Name --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Menu Name
                                <span class="badge bg-blue-lt ms-1">{{ strtoupper($activeLocale) }}</span>
                            </label>
                            <input type="text"
                                class="form-control @error('name.'.$activeLocale) is-invalid @enderror"
                                wire:model.live="name.{{ $activeLocale }}"
                                dir="{{ $activeDir }}"
                                placeholder="Enter menu name in {{ $activeLanguages->where('code', $activeLocale)->first()?->name }}"
                                wire:key="name-input-{{ $activeLocale }}">
                            @error('name.'.$activeLocale)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-hint">Switch the language above to add translations in other languages.</div>
                        </div>

                        {{-- Slug (auto-generated) --}}
                        <div class="mb-3">
                            <label class="form-label">Slug <span class="text-muted small">(auto-generated from default language name)</span></label>
                            <input type="text"
                                class="form-control @error('slug') is-invalid @enderror"
                                wire:model="slug"
                                placeholder="e.g. footer-company">
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-hint">Used in code to load the menu: <code>Menu::where('slug', 'your-slug')</code></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">Close</button>
                    @if ($updateMode)
                        <button type="button" class="btn btn-primary" wire:click="update">
                            <i class="ti ti-device-floppy me-1"></i> Save Changes
                        </button>
                    @else
                        <button type="button" class="btn btn-primary" wire:click="store">
                            <i class="ti ti-plus me-1"></i> Create Menu
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
