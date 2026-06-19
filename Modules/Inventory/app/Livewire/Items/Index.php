<?php

namespace Modules\Inventory\Livewire\Items;

use App\Livewire\WithFilters;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Inventory\Exports\ItemsExport;
use Modules\Inventory\Imports\ItemsImport;
use Modules\Inventory\Imports\ProductsImport;
use Modules\Inventory\Imports\ServicesImport;
use Modules\Inventory\Imports\SparePartsImport;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCategory;

class Index extends Component
{
    use WithFileUploads, WithFilters, WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public $showImportModal = false;

    public bool $showDetailedImportModal = false;

    public string $importMode = 'price';

    public int $perPage;

    public $search = '';

    public $importFile;

    public $detailedImportFile;

    public $model;

    public $type;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => ''],
        'sortDirection' => ['except' => ''],
        'perPage' => ['except' => 10],
        'filters' => ['except' => []],
    ];

    public function mount($type = null)
    {
        $this->authorize('read_items');
        $this->type = $type;
        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'desc'; // Default sort direction
        $this->perPage = 500; // Default pagination limit
        $this->orderable = ['id', 'reference', 'name', 'status'];

        $this->model = new Item;
        $this->initFilters($this->model);
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function openImportModal(?string $mode = null): void
    {
        $this->showImportModal = true;
        if ($this->type === 'service') {
            $this->importMode = 'price';
        } elseif (in_array($mode, ['price', 'quantity'], true)) {
            $this->importMode = $mode;
        }
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->importMode = 'price';
    }

    public function openDetailedImportModal(): void
    {
        $this->showDetailedImportModal = true;
    }

    public function closeDetailedImportModal(): void
    {
        $this->showDetailedImportModal = false;
    }

    public function delete()
    {
        $this->authorize('delete_items');
        Item::find($this->deleteId)->delete();
        $this->closeModal();
        session()->flash('message', 'Item deleted successfully.');
    }

    public function render()
    {
        $queryBase = Item::query();

        if ($this->type && empty($this->filters['type'])) {
            $queryBase->where('type', $this->type);
        }

        $itemsCount = $queryBase->clone()->count();
        $activeItemsCount = $queryBase->clone()->where('status', 'active')->count();
        $inactiveItemsCount = $queryBase->clone()->where('status', 'inactive')->count();

        $query = $queryBase->clone();

        if (filled($this->search)) {
            $search = trim((string) $this->search);
            $langs = system_setting('active_languages', ['ar']);
            $activeLanguages = is_string($langs) ? (json_decode($langs, true) ?? [$langs]) : $langs;

            $query->where(function ($q) use ($search, $activeLanguages) {
                $q->where('reference', 'like', '%'.$search.'%')
                    ->orWhere('model_number', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%')
                    ->orWhere('name->en', 'like', '%'.$search.'%');
                foreach ($activeLanguages as $lang) {
                    $q->orWhere('name->'.$lang, 'like', '%'.$search.'%');
                }
            });
        }

        $query = $this->applyFilters($query, $this->model);

        if (! in_array($this->sortBy, $this->orderable, true)) {
            $this->sortBy = 'id';
        }

        $items = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $categories = ItemCategory::all();

        $currentUser = Auth::user();
        $actions = [];
        if ($currentUser?->can('create_items')) {
            $actions[] = [
                'label' => 'Add New '.(Item::TYPE_SELECT[$this->type] ?? 'Item'),
                'icon' => 'add',
                'url' => url('inventory/items/create').($this->type ? '?type='.$this->type : ''),
                'class' => 'bg-gradient-dark',
            ];
            $importItems = [];
            if (! $this->type) {
                $importItems[] = [
                    'label' => 'Import Products',
                    'icon' => 'upload',
                    'action' => 'openImportModal',
                ];
            } elseif ($this->type === 'product') {
                $importItems[] = [
                    'label' => 'Product Details',
                    'icon' => 'upload',
                    'action' => 'openDetailedImportModal',
                ];
                $importItems[] = [
                    'label' => 'Prices',
                    'icon' => 'upload',
                    'action' => "openImportModal('price')",
                ];
                $importItems[] = [
                    'label' => 'Quantities',
                    'icon' => 'upload',
                    'action' => "openImportModal('quantity')",
                ];
            } elseif ($this->type === 'spare_part') {
                $importItems[] = [
                    'label' => 'Prices',
                    'icon' => 'upload',
                    'action' => "openImportModal('price')",
                ];
                $importItems[] = [
                    'label' => 'Quantities',
                    'icon' => 'upload',
                    'action' => "openImportModal('quantity')",
                ];
            } elseif ($this->type === 'service') {
                $importItems[] = [
                    'label' => 'Prices',
                    'icon' => 'upload',
                    'action' => "openImportModal('price')",
                ];
            }

            if (count($importItems) > 0) {
                $actions[] = [
                    'label' => 'Import',
                    'icon' => 'upload',
                    'class' => 'bg-gradient-info',
                    'items' => $importItems,
                ];
            }
        }
        if ($this->type === 'product') {
            $actions[] = [
                'label' => 'Export',
                'icon' => 'download',
                'class' => 'bg-gradient-light',
                'items' => [
                    [
                        'label' => 'Products',
                        'icon' => 'download',
                        'action' => 'exportDetailed',
                    ],
                    [
                        'label' => 'Prices',
                        'icon' => 'download',
                        'action' => 'exportPrices',
                    ],
                    [
                        'label' => 'Quantities',
                        'icon' => 'download',
                        'action' => 'exportQuantities',
                    ],
                ],
            ];
        } elseif (in_array($this->type, ['spare_part', 'service'], true)) {
            $exportItems = [
                [
                    'label' => 'Prices',
                    'icon' => 'download',
                    'action' => 'exportPrices',
                ],
            ];
            if ($this->type === 'spare_part') {
                $exportItems[] = [
                    'label' => 'Quantities',
                    'icon' => 'download',
                    'action' => 'exportQuantities',
                ];
            }

            $actions[] = [
                'label' => 'Export',
                'icon' => 'download',
                'class' => 'bg-gradient-light',
                'items' => $exportItems,
            ];
        } elseif ($this->type) {
            $labelType = Item::TYPE_SELECT[$this->type] ?? 'Item';
            $actions[] = [
                'label' => 'Export '.$labelType,
                'icon' => 'download',
                'action' => 'export',
                'class' => 'bg-gradient-light',
            ];
        } else {
            $actions[] = [
                'label' => 'Export',
                'icon' => 'download',
                'action' => 'export',
                'class' => 'bg-gradient-light',
            ];
        }

        return view('inventory::livewire.items.index', compact(
            'items',
            'categories',
            'itemsCount',
            'activeItemsCount',
            'inactiveItemsCount',
            'actions'
        ));
    }

    public function export()
    {
        $this->authorize('read_items');

        $prefix = $this->type ? ($this->type.'s') : 'items';
        $filename = date('Y-m-d').'-'.$prefix.'.xlsx';

        return Excel::download(new ItemsExport('list', $this->type, $this->search, $this->filters), $filename);
    }

    public function exportDetailed()
    {
        $this->authorize('read_items');
        if ($this->type !== 'product') {
            return;
        }

        $filename = date('Y-m-d').'-product-details.xlsx';

        return Excel::download(new ItemsExport('detailed', $this->type, $this->search, $this->filters), $filename);
    }

    public function exportPrices()
    {
        $this->authorize('read_items');
        if (! in_array($this->type, ['spare_part', 'product', 'service'], true)) {
            return;
        }

        $prefix = $this->type ? $this->type : 'items';
        $filename = date('Y-m-d').'-'.$prefix.'-prices.xlsx';

        return Excel::download(new ItemsExport('prices', $this->type, $this->search, $this->filters), $filename);
    }

    public function exportQuantities()
    {
        $this->authorize('read_items');
        if (! in_array($this->type, ['spare_part', 'product'], true)) {
            return;
        }

        $prefix = $this->type ? $this->type : 'items';
        $filename = date('Y-m-d').'-'.$prefix.'-quantities.xlsx';

        return Excel::download(new ItemsExport('quantities', $this->type, $this->search, $this->filters), $filename);
    }

    public function import()
    {
        $this->authorize('create_items');
        $this->validate([
            'importFile' => 'required|mimes:xlsx,csv',
        ]);
        set_time_limit(300);
        try {
            if (in_array($this->type, ['spare_part', 'product', 'service'], true)) {
                $mode = $this->type === 'service' ? 'price' : $this->importMode;

                if ($this->type === 'spare_part') {
                    $importer = new SparePartsImport($mode);
                } elseif ($this->type === 'product') {
                    $importer = new ProductsImport($mode);
                } else {
                    $importer = new ServicesImport;
                }

                Excel::import($importer, $this->importFile->getRealPath());

                $action = $mode === 'quantity' ? 'adjusted' : 'updated';
                $msg = "Import complete: {$importer->created} created, {$importer->updated} {$action}";
                if ($importer->skipped > 0) {
                    $msg .= ", {$importer->skipped} skipped";
                }
                if (! empty($importer->errors)) {
                    $msg .= '<br>'.implode('<br>', array_map('e', $importer->errors));
                    session()->flash('error', $msg.'.');
                } else {
                    session()->flash('message', $msg.'.');
                }
            } else {
                Excel::import(new ItemsImport, $this->importFile->getRealPath());
                session()->flash('message', 'Items imported successfully.');
            }

            $this->closeImportModal();
            $this->reset('importFile');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()} failed: ".implode(', ', $failure->errors());
            }
            session()->flash('error', implode('<br>', $errors));
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while importing: '.$e->getMessage());
        }
    }

    public function importDetailed(): void
    {
        $this->authorize('create_items');
        if ($this->type !== 'product') {
            return;
        }

        $this->validate([
            'detailedImportFile' => 'required|mimes:xlsx,csv',
        ]);

        set_time_limit(300);
        try {
            Excel::import(new ItemsImport, $this->detailedImportFile->getRealPath());
            session()->flash('message', 'Products imported successfully.');
            $this->closeDetailedImportModal();
            $this->reset('detailedImportFile');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()} failed: ".implode(', ', $failure->errors());
            }
            session()->flash('error', implode('<br>', $errors));
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while importing: '.$e->getMessage());
        }
    }
}
