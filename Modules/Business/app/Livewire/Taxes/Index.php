<?php

namespace Modules\Business\Livewire\Taxes;

use App\Livewire\WithFilters;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Business\Models\Company;
use Modules\Business\Models\Tax;

class Index extends Component
{
    use WithFilters, WithModalTrait, WithPagination, WithSorting;

    public $search = '';

    public $perPage = 10;

    public $name = '';

    // Removed: public $code, $symbol_left, $symbol_right
    public $rate = 0;

    public $status = 'active';

    public $is_default = false;

    public $company_id = null;

    public $editId;

    public $updateMode = false;

    public $model;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'name' => 'required|string|max:255',
        'rate' => 'required|numeric|min:0',
        'status' => 'required|in:active,inactive',
        'is_default' => 'boolean',
        'company_id' => 'nullable|exists:companies,id',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'id'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
        'filters' => ['except' => []],
    ];

    public function mount()
    {
        $this->sortBy = 'id';
        $this->sortDirection = 'asc';
        $this->orderable = ['id', 'name', 'rate', 'status', 'is_default', 'company_id'];
        $this->model = new Tax;
        $this->initFilters($this->model);
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->rate = 0;
        $this->status = 'active';
        $this->is_default = false;
        $this->company_id = null;
    }

    public function store()
    {
        $validated = $this->validate();
        Tax::create($validated);
        $this->closeModal();
        session()->flash('success', 'Tax created successfully.');
    }

    public function edit($id)
    {
        $tax = Tax::findOrFail($id);
        $this->deleteId = null;
        $this->name = $tax->name;
        $this->rate = $tax->rate;
        $this->status = $tax->status;
        $this->is_default = (bool) $tax->is_default;
        $this->company_id = $tax->company_id;
        $this->editId = $tax->id;
        $this->openModal('edit');
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate();
        $tax = Tax::findOrFail($this->editId);
        $tax->update([
            'name' => $this->name,
            'rate' => $this->rate,
            'status' => $this->status,
            'is_default' => $this->is_default,
            'company_id' => $this->company_id,
        ]);
        $this->closeModal();
        session()->flash('success', 'Tax updated successfully.');
    }

    public function delete()
    {
        $tax = Tax::findOrFail($this->deleteId);
        $tax->delete();
        $this->closeDeleteModal();
        session()->flash('success', 'Tax deleted successfully.');
    }

    public function render()
    {
        $baseQuery = Tax::query();

        $taxesCount = $baseQuery->clone()->count();
        $activeTaxesCount = $baseQuery->clone()->where('status', 'active')->count();
        $inactiveTaxesCount = $baseQuery->clone()->where('status', 'inactive')->count();
        $defaultTaxesCount = $baseQuery->clone()->where('is_default', true)->count();

        $query = $baseQuery->clone()->with('company');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        $query = $this->applyFilters($query, $this->model);

        $taxes = $query
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $companies = Company::select('id', 'name')->get();

        return view('business::livewire.taxes.index', compact(
            'taxes',
            'companies',
            'taxesCount',
            'activeTaxesCount',
            'inactiveTaxesCount',
            'defaultTaxesCount'
        ) + [
            'model' => $this->model,
        ]);
    }
}
