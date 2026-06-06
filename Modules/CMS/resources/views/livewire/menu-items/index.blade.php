<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Menu Item Management</h2>
                    <div class="text-muted small mt-1">
                        <i class="ti ti-drag-drop me-1"></i> Drag rows to reorder — saves automatically
                    </div>
                </div>
                <div class="col-auto ms-auto d-flex align-items-center gap-2">
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
                        <i class="ti ti-plus me-1"></i> Add New Menu Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @include('components.tabler.alerts')

            <div class="card">
                <div class="card-header border-bottom py-2">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <form wire:submit="filter" class="flex-grow-1 me-3">
                            <input type="text" class="form-control" wire:model="search"
                                placeholder="Search menu items...">
                        </form>
                        <span id="mi-saving" class="text-muted small d-none me-2">
                            <span class="spinner-border spinner-border-sm me-1"></span>Saving…
                        </span>
                        <span id="mi-saved" class="text-success small d-none me-2">
                            <i class="ti ti-check me-1"></i>Order saved
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:36px;"></th>
                                <th>Title ({{ strtoupper($activeLocale) }})</th>
                                <th>URL</th>
                                <th style="width:70px;">Order</th>
                                <th style="width:100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="mi-sortable" wire:ignore>
                            @foreach($menuItems as $index => $menuItem)
                                <tr data-id="{{ $menuItem->id }}" data-parent-id="" class="mi-row">
                                    <td class="mi-handle text-muted text-center" style="cursor:grab; width:36px;">
                                        <i class="ti ti-grip-vertical" style="font-size:1.2rem; opacity:.4;"></i>
                                    </td>
                                    <td>
                                        @if($menuItem->icon)<span class="me-1">{{ $menuItem->icon }}</span>@endif
                                        <strong>{{ $menuItem->getTranslation('title', $activeLocale, false) ?: $menuItem->getTranslation('title', 'en', false) }}</strong>
                                    </td>
                                    <td><code class="small text-muted">{{ $menuItem->url }}</code></td>
                                    <td class="mi-order text-muted small">{{ $menuItem->order }}</td>
                                    <td>
                                        <button wire:click="edit({{ $menuItem->id }})" class="btn btn-sm btn-icon btn-ghost-primary"><i class="ti ti-pencil"></i></button>
                                        <button wire:click="confirmDelete({{ $menuItem->id }})" class="btn btn-sm btn-icon btn-ghost-danger" wire:confirm="Delete this menu item?"><i class="ti ti-trash"></i></button>
                                    </td>
                                </tr>
                                @foreach($menuItem->children as $i2 => $child)
                                    <tr data-id="{{ $child->id }}" data-parent-id="{{ $menuItem->id }}" class="mi-row mi-child">
                                        <td class="mi-handle text-muted text-center" style="cursor:grab;">
                                            <i class="ti ti-grip-vertical" style="font-size:1.2rem; opacity:.3;"></i>
                                        </td>
                                        <td class="ps-4">
                                            <span class="text-muted me-1">↳</span>
                                            @if($child->icon)<span class="me-1">{{ $child->icon }}</span>@endif
                                            {{ $child->getTranslation('title', $activeLocale, false) ?: $child->getTranslation('title', 'en', false) }}
                                        </td>
                                        <td><code class="small text-muted">{{ $child->url }}</code></td>
                                        <td class="mi-order text-muted small">{{ $child->order }}</td>
                                        <td>
                                            <button wire:click="edit({{ $child->id }})" class="btn btn-sm btn-icon btn-ghost-primary"><i class="ti ti-pencil"></i></button>
                                            <button wire:click="confirmDelete({{ $child->id }})" class="btn btn-sm btn-icon btn-ghost-danger" wire:confirm="Delete this menu item?"><i class="ti ti-trash"></i></button>
                                        </td>
                                    </tr>
                                    @foreach($child->children as $i3 => $grandchild)
                                        <tr data-id="{{ $grandchild->id }}" data-parent-id="{{ $child->id }}" class="mi-row mi-grandchild">
                                            <td class="mi-handle text-muted text-center" style="cursor:grab;">
                                                <i class="ti ti-grip-vertical" style="font-size:1.2rem; opacity:.2;"></i>
                                            </td>
                                            <td class="ps-5">
                                                <span class="text-muted me-1">&nbsp;↳</span>
                                                @if($grandchild->icon)<span class="me-1">{{ $grandchild->icon }}</span>@endif
                                                {{ $grandchild->getTranslation('title', $activeLocale, false) ?: $grandchild->getTranslation('title', 'en', false) }}
                                            </td>
                                            <td><code class="small text-muted">{{ $grandchild->url }}</code></td>
                                            <td class="mi-order text-muted small">{{ $grandchild->order }}</td>
                                            <td>
                                                <button wire:click="edit({{ $grandchild->id }})" class="btn btn-sm btn-icon btn-ghost-primary"><i class="ti ti-pencil"></i></button>
                                                <button wire:click="confirmDelete({{ $grandchild->id }})" class="btn btn-sm btn-icon btn-ghost-danger" wire:confirm="Delete this menu item?"><i class="ti ti-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal modal-blur fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog"
        style="@if($showModal) background: rgba(0,0,0,0.5); @endif"
        @if($showModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $updateMode ? 'Edit' : 'Create' }} Menu Item</h5>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm border-secondary" wire:model.live="activeLocale" style="width:auto;">
                            @foreach ($activeLanguages as $lang)
                                <option value="{{ $lang->code }}">🌐 {{ $lang->name }} ({{ strtoupper($lang->code) }})</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                </div>
                <div class="modal-body">
                    @php $activeDir = $activeLanguages->where('code', $activeLocale)->first()?->direction ?? 'ltr'; @endphp
                    <form>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Title <span class="badge bg-blue-lt ms-1">{{ strtoupper($activeLocale) }}</span>
                            </label>
                            <input type="text"
                                class="form-control @error('title.'.$activeLocale) is-invalid @enderror"
                                wire:model="title.{{ $activeLocale }}"
                                dir="{{ $activeDir }}"
                                placeholder="Enter title in {{ $activeLanguages->where('code', $activeLocale)->first()?->name }}"
                                wire:key="title-input-{{ $activeLocale }}">
                            @error('title.'.$activeLocale) <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-hint">Switch language above to add other translations.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">URL</label>
                            <input type="text" class="form-control @error('url') is-invalid @enderror"
                                wire:model="url" placeholder="/shop or https://example.com">
                            @error('url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon <span class="text-muted small">(emoji or class)</span></label>
                            <input type="text" class="form-control @error('icon') is-invalid @enderror"
                                wire:model="icon" placeholder="🛒">
                            @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Sort Order</label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror" wire:model="order">
                                @error('order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Parent Menu Item <span class="text-muted small">(optional)</span></label>
                                <select class="form-select @error('parentId') is-invalid @enderror" wire:model="parentId">
                                    <option value="">— None (Top Level) —</option>
                                    @foreach($allMenuItems as $item)
                                        @if($item->id !== $menuItemId)
                                            <option value="{{ $item->id }}">
                                                {{ $item->getTranslation('title', $activeLocale, false) ?: $item->getTranslation('title', 'en', false) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('parentId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
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
                            <i class="ti ti-plus me-1"></i> Create
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .mi-child     { background: #f8fafc; }
        .mi-grandchild{ background: #f1f5f9; }
        .mi-handle:hover i { opacity: 1 !important; }
        .sortable-ghost   { opacity: .4; background: #dbeafe !important; }
        .sortable-chosen  { box-shadow: 0 4px 12px rgba(0,0,0,.15); }
    </style>

    {{-- SortableJS — placed inline like page-builder, using wire.call() pattern --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
    (function () {
        var sortable = null;

        function getWire() {
            var root = document.querySelector('#mi-sortable')?.closest('[wire\\:id]');
            return root ? Livewire.find(root.getAttribute('wire:id')) : null;
        }

        function initSortable() {
            var tbody = document.getElementById('mi-sortable');
            if (!tbody) return;
            if (sortable) { sortable.destroy(); sortable = null; }

            sortable = Sortable.create(tbody, {
                handle: '.mi-handle',
                animation: 180,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function () {
                    var rows = tbody.querySelectorAll('tr.mi-row');
                    var payload = [];
                    var order = 1;
                    rows.forEach(function (row) {
                        payload.push({
                            id:       parseInt(row.dataset.id),
                            order:    order++,
                            parentId: row.dataset.parentId ? parseInt(row.dataset.parentId) : null,
                        });
                        var cell = row.querySelector('.mi-order');
                        if (cell) cell.textContent = order - 1;
                    });

                    var saving = document.getElementById('mi-saving');
                    var saved  = document.getElementById('mi-saved');
                    if (saving) saving.classList.remove('d-none');
                    if (saved)  saved.classList.add('d-none');

                    var wire = getWire();
                    if (wire) {
                        wire.call('updateOrder', payload).then(function () {
                            if (saving) saving.classList.add('d-none');
                            if (saved)  saved.classList.remove('d-none');
                            setTimeout(function () {
                                if (saved) saved.classList.add('d-none');
                            }, 2500);
                        });
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initSortable);
        document.addEventListener('livewire:updated', function () { setTimeout(initSortable, 150); });
    })();
    </script>
</div>
