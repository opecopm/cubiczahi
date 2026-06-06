<?php

namespace Modules\Business\Livewire\Currencies;

use App\Livewire\WithFilters;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Business\Models\Currency;

class Index extends Component
{
    use WithFilters, WithModalTrait, WithPagination, WithSorting;

    public $search = '';

    public $perPage = 10;

    public $name = '';

    public $rate = 1.0;

    public $status = true; // UI toggle; persisted as 'active'/'inactive'

    public $is_default = false;

    public $editId;

    public $updateMode = false;

    public $code = '';

    public $symbol_left = '';

    public $symbol_right = '';

    public $model;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'name' => 'required|string|max:255',
        'rate' => 'required|numeric|min:0',
        'status' => 'boolean',
        'is_default' => 'boolean',
        'code' => 'nullable|string|max:20',
        'symbol_left' => 'nullable|string|max:10',
        'symbol_right' => 'nullable|string|max:10',
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
        $this->orderable = ['id', 'name', 'code', 'rate', 'status', 'is_default'];
        $this->model = new Currency;
        $this->initFilters($this->model);
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->rate = 1.0;
        $this->status = true;
        $this->is_default = false;
        $this->editId = null;
        $this->code = '';
        $this->symbol_left = '';
        $this->symbol_right = '';
    }

    public function store()
    {
        $validated = $this->validate();
        $statusStr = $this->status ? 'active' : 'inactive';

        Currency::create([
            'name' => $this->name,
            'rate' => $this->rate,
            'status' => $statusStr,
            'is_default' => $this->is_default,
            'code' => $this->code,
            'symbol_left' => $this->symbol_left,
            'symbol_right' => $this->symbol_right,
        ]);

        $this->closeModal();
        session()->flash('success', 'Currency created successfully.');
    }

    public function edit($id)
    {
        $currency = Currency::findOrFail($id);
        $this->deleteId = null;
        $this->name = $currency->name;
        $this->rate = $currency->rate;
        $this->status = ($currency->status === 'active');
        $this->is_default = (bool) $currency->is_default;
        $this->editId = $currency->id;
        $this->openModal();
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate();
        $currency = Currency::findOrFail($this->editId);
        $statusStr = $this->status ? 'active' : 'inactive';

        $currency->update([
            'name' => $this->name,
            'rate' => $this->rate,
            'status' => $statusStr,
            'is_default' => $this->is_default,
            'code' => $this->code,
            'symbol_left' => $this->symbol_left,
            'symbol_right' => $this->symbol_right,
        ]);

        $this->closeModal();
        session()->flash('success', 'Currency updated successfully.');
    }

    public function delete()
    {
        $currency = Currency::findOrFail($this->deleteId);
        $currency->delete();
        $this->closeDeleteModal();
        session()->flash('success', 'Currency deleted successfully.');
    }

    public function render()
    {
        $baseQuery = Currency::query();

        $currenciesCount = $baseQuery->clone()->count();
        $activeCurrenciesCount = $baseQuery->clone()->where('status', 'active')->count();
        $inactiveCurrenciesCount = $baseQuery->clone()->where('status', 'inactive')->count();
        $defaultCurrenciesCount = $baseQuery->clone()->where('is_default', true)->count();

        $query = $baseQuery->clone();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        $query = $this->applyFilters($query, $this->model);

        $currencies = $query
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('business::livewire.currencies.index', compact(
            'currencies',
            'currenciesCount',
            'activeCurrenciesCount',
            'inactiveCurrenciesCount',
            'defaultCurrenciesCount'
        ) + [
            'model' => $this->model,
        ]);
    }
}
