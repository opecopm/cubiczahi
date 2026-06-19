<div>
    @component('admin.partials.page.inner-header', [
        'title' => $type ? \Modules\Inventory\Models\Item::TYPE_SELECT[$type] . ' Management' : 'Item Management',
        'breadcrumbs' => [
            [
                'label' => $type ? \Modules\Inventory\Models\Item::TYPE_SELECT[$type] : 'Items',
                'active' => true,
            ],
        ],
    ])
        @slot('actions')
            <div class="btn-list">
                @can('create_items')
                    <a href="{{ route('admin.inventory.items.create') }}{{ $type ? ('?type=' . $type) : '' }}" class="btn btn-primary d-none d-sm-inline-block">
                        <i class="ti ti-plus me-1"></i>
                        Add New {{ \Modules\Inventory\Models\Item::TYPE_SELECT[$type] ?? 'Item' }}
                    </a>
                @endcan

                @can('create_items')
                    @php
                        $hasImport =
                            empty($type) ||
                            in_array($type, ['product', 'spare_part', 'service'], true);
                    @endphp
                    @if ($hasImport)
                        <div class="dropdown d-none d-sm-inline-block">
                            <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"
                                type="button">
                                <i class="ti ti-file-import me-1"></i>
                                Import
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                @if (empty($type))
                                    <button type="button" class="dropdown-item" wire:click="openImportModal">
                                        Products
                                    </button>
                                @elseif ($type === 'product')
                                    <button type="button" class="dropdown-item"
                                        wire:click="openDetailedImportModal">
                                        Product Details
                                    </button>
                                    <button type="button" class="dropdown-item"
                                        wire:click="openImportModal('price')">
                                        Prices
                                    </button>
                                    <button type="button" class="dropdown-item"
                                        wire:click="openImportModal('quantity')">
                                        Quantities
                                    </button>
                                @elseif ($type === 'spare_part')
                                    <button type="button" class="dropdown-item"
                                        wire:click="openImportModal('price')">
                                        Prices
                                    </button>
                                    <button type="button" class="dropdown-item"
                                        wire:click="openImportModal('quantity')">
                                        Quantities
                                    </button>
                                @elseif ($type === 'service')
                                    <button type="button" class="dropdown-item"
                                        wire:click="openImportModal('price')">
                                        Prices
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                @endcan

                @php
                    $hasExport =
                        empty($type) ||
                        in_array($type, ['product', 'spare_part', 'service'], true);
                @endphp
                @if ($hasExport)
                    <div class="dropdown d-none d-sm-inline-block">
                        <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"
                            type="button">
                            <i class="ti ti-file-export me-1"></i>
                            Export
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            @if ($type === 'product')
                                <button type="button" class="dropdown-item" wire:click="exportDetailed">
                                    Product Details
                                </button>
                                <button type="button" class="dropdown-item" wire:click="exportPrices">
                                    Prices
                                </button>
                                <button type="button" class="dropdown-item" wire:click="exportQuantities">
                                    Quantities
                                </button>
                            @elseif ($type === 'spare_part')
                                <button type="button" class="dropdown-item" wire:click="exportPrices">
                                    Prices
                                </button>
                                <button type="button" class="dropdown-item" wire:click="exportQuantities">
                                    Quantities
                                </button>
                            @elseif ($type === 'service')
                                <button type="button" class="dropdown-item" wire:click="exportPrices">
                                    Prices
                                </button>
                            @else
                                <button type="button" class="dropdown-item" wire:click="export">
                                    Export
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endslot
    @endcomponent

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards mb-3">
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.status', '')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="bg-blue text-white avatar">{{ $itemsCount }}</span></div>
                                <div class="col"><div class="text-secondary">All Items</div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.status', 'active')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="bg-green text-white avatar">{{ $activeItemsCount }}</span></div>
                                <div class="col"><div class="text-secondary">Active</div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-sm" style="cursor:pointer" wire:click="$set('filters.status', 'inactive')">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="bg-secondary text-white avatar">{{ $inactiveItemsCount }}</span></div>
                                <div class="col"><div class="text-secondary">Inactive</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('components.tabler.alerts')

            <div class="card">
                <div class="card-header">
                    <div class="row g-2 w-100">
                        <div class="col">
                            <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search...">
                        </div>
                        @foreach($model->filterable as $field => $meta)
                            @if($type && $field === 'type')
                                @continue
                            @endif
                            <div class="col">
                                @if(($meta['type'] ?? null) === 'select')
                                    <select class="form-select" wire:model.live="filters.{{ $field }}">
                                        <option value="">{{ ucfirst(str_replace('_',' ',$field)) }}: All</option>
                                        @foreach(($meta['options'] ?? []) as $key => $val)
                                            <option value="{{ $key }}">{{ is_array($val) ? ($val['name'] ?? reset($val)) : $val }}</option>
                                        @endforeach
                                    </select>
                                @elseif(($meta['type'] ?? null) === 'date')
                                    <input type="date" class="form-control" wire:model.live="filters.{{ $field }}">
                                @else
                                    <input type="text" class="form-control" wire:model.live="filters.{{ $field }}" placeholder="Search {{ ucfirst(str_replace('_',' ',$field)) }}">
                                @endif
                            </div>
                        @endforeach
                        <div class="col-auto">
                            <button wire:click="resetFilters" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>ID @include('components.table.sort', ['field' => 'id'])</th>
                                <th>Reference @include('components.table.sort', ['field' => 'reference'])</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Model Number</th>
                                <th>Name @include('components.table.sort', ['field' => 'name'])</th>
                                <th class="text-end">Sell Price</th>
                                <th>Status @include('components.table.sort', ['field' => 'status'])</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td class="text-secondary">{{ $item->id }}</td>
                                    <td><a href="{{ route('admin.inventory.items.show', $item->id) }}">{{ $item->reference }}</a></td>
                                    <td>{{ \Modules\Inventory\Models\Item::TYPE_SELECT[$item->type] ?? $item->type }}</td>
                                    <td>{{ $item->category->name ?? '' }}</td>
                                    <td>{{ $item->model_number ?? '' }}</td>
                                    <td>{{ collect($item->getTranslations('name'))->filter()->join(' / ') }}</td>
                                    <td class="text-end">
                                        {{ @$item->price('sell') ? $item->price('sell')->price : '—' }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->status === 'active' ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ $item->status_label ?? 'NA' }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ route('admin.inventory.items.show', $item->id) }}" class="dropdown-item">View</a>
                                                @can('update_items')
                                                    <a href="{{ route('admin.inventory.items.edit', ['item' => $item->id, 'step' => 1]) }}" class="dropdown-item">Edit</a>
                                                @endcan
                                                @can('delete_items')
                                                    <button wire:click="confirmDelete({{ $item->id }})" wire:confirm="Are you sure you want to delete this item?" class="dropdown-item text-danger">Delete</button>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-secondary py-4">No items found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($items->hasPages())
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <small class="text-secondary">
                        Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }}
                    </small>
                    {{ $items->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade @if($showImportModal) show d-block @endif" tabindex="-1" role="dialog"
        style="background: rgba(0,0,0,0.5);" @if($showImportModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if ($type)
                            Import {{ \Modules\Inventory\Models\Item::TYPE_SELECT[$type] ?? 'Items' }}
                        @else
                            Import Items
                        @endif
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeImportModal"></button>
                </div>
                <div class="modal-body">
                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {!! session('error') !!}
                        </div>
                    @endif

                    <form wire:submit.prevent="import">
                        <div class="row g-3">
                            @if ($type === 'spare_part')
                                <div class="col-12">
                                    <label class="form-label">Import Type</label>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <label class="form-check">
                                            <input class="form-check-input" type="radio" wire:model.live="importMode" value="price">
                                            <span class="form-check-label">Price Update</span>
                                        </label>
                                        <label class="form-check">
                                            <input class="form-check-input" type="radio" wire:model.live="importMode" value="quantity">
                                            <span class="form-check-label">Quantity Update</span>
                                        </label>
                                    </div>
                                </div>
                            @endif

                            @if ($type === 'product')
                                <div class="col-12">
                                    <label class="form-label">Import Type</label>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <label class="form-check">
                                            <input class="form-check-input" type="radio" wire:model.live="importMode" value="price">
                                            <span class="form-check-label">Price Update</span>
                                        </label>
                                        <label class="form-check">
                                            <input class="form-check-input" type="radio" wire:model.live="importMode" value="quantity">
                                            <span class="form-check-label">Quantity Update</span>
                                        </label>
                                    </div>
                                </div>
                            @endif

                            @if ($type === 'service')
                                <div class="col-12">
                                    <div class="text-secondary">
                                        Services import supports Price Update only.
                                    </div>
                                </div>
                            @endif

                            <div class="col-12">
                                <label for="import_file" class="form-label">Upload File</label>
                                <input type="file" class="form-control" id="import_file" wire:model="importFile">
                                <div wire:loading wire:target="importFile" class="text-info mt-2">
                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                    Uploading file...
                                </div>
                                @error('importFile')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <div class="text-secondary">
                                    Download a sample file for reference:
                                    <a href="{{ url('import-templates/items_import_template.xlsx') }}" class="link-primary">Sample File</a>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeImportModal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" @if(!$importFile) disabled @endif wire:loading.attr="disabled">
                                <span wire:loading wire:target="import" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span wire:loading.remove wire:target="import">Import</span>
                                <span wire:loading wire:target="import">Importing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade @if($showDetailedImportModal) show d-block @endif" tabindex="-1" role="dialog"
        style="background: rgba(0,0,0,0.5);" @if($showDetailedImportModal) aria-modal="true" @else aria-hidden="true" @endif>
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Product Details</h5>
                    <button type="button" class="btn-close" wire:click="closeDetailedImportModal"></button>
                </div>
                <div class="modal-body">
                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {!! session('error') !!}
                        </div>
                    @endif

                    <form wire:submit.prevent="importDetailed">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="detailed_import_file" class="form-label">Upload File</label>
                                <input type="file" class="form-control" id="detailed_import_file" wire:model="detailedImportFile">
                                <div wire:loading wire:target="detailedImportFile" class="text-info mt-2">
                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                    Uploading file...
                                </div>
                                @error('detailedImportFile')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <div class="text-secondary">
                                    Download a sample file for reference:
                                    <a href="{{ url('import-templates/items_import_template.xlsx') }}" class="link-primary">Sample File</a>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeDetailedImportModal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" @if(!$detailedImportFile) disabled @endif wire:loading.attr="disabled">
                                <span wire:loading wire:target="importDetailed" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span wire:loading.remove wire:target="importDetailed">Import</span>
                                <span wire:loading wire:target="importDetailed">Importing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
