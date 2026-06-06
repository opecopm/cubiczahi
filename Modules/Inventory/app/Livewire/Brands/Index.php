<?php

namespace Modules\Inventory\Livewire\Brands;

use App\Livewire\WithFilters;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Inventory\Models\Brand;

class Index extends Component
{
    use WithFilters, WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public string $search = '';

    public string $name = '';

    public ?int $brandId = null;

    public $model;

    public bool $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => ['except' => ''],
        'perPage' => ['except' => 10],
        'filters' => ['except' => []],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|min:2|unique:brands,name,'.$this->brandId,
        ];
    }

    public function mount()
    {
        $this->authorize('read_brands');
        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'desc'; // Default sort direction
        $this->perPage = 100; // Default pagination limit
        $this->orderable = ['id', 'name'];

        $this->model = new Brand;
        $this->initFilters($this->model);
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->authorize('create_brands');
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function filter()
    {
        $this->resetPage();
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->brandId = null;
        $this->updateMode = false;
    }

    public function store()
    {
        $this->authorize('create_brands');
        $this->validate();
        Brand::create(['name' => $this->name]);
        session()->flash('message', 'Brand created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $this->authorize('update_brands');
        $brand = Brand::findOrFail($id);
        $this->brandId = $brand->id;
        $this->name = $brand->name;
        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->authorize('update_brands');
        $this->validate();
        $brand = Brand::findOrFail($this->brandId);
        $brand->update(['name' => $this->name]);
        session()->flash('message', 'Brand updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        $this->authorize('delete_brands');
        $brand = Brand::find($this->deleteId);
        if ($brand) {
            $brand->delete();
        }
        session()->flash('message', 'Brand deleted successfully.');
        $this->closeModal();
    }

    public function render()
    {
        $query = Brand::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%');
            });
        }

        $query = $this->applyFilters($query, $this->model);

        $brands = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('inventory::livewire.brands.index', compact('brands'));
    }
}
