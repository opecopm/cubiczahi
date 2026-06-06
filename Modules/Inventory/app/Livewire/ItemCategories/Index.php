<?php

namespace Modules\Inventory\Livewire\ItemCategories;

use App\Livewire\WithFilters;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Inventory\Models\ItemCategory;

class Index extends Component
{
    use WithFilters, WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public string $search = '';

    public string $code = '';

    public string $name = '';

    public $parent_id;

    public $categoryId;

    public $model;

    public $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => ['except' => ''],
        'perPage' => ['except' => 10],
        'filters' => ['except' => []],
    ];

    protected function rules()
    {
        return [
            'code' => 'nullable|string|max:255|unique:item_categories,code,'.$this->categoryId,
            'name' => 'required|min:2|unique:item_categories,name,'.$this->categoryId,
            'parent_id' => 'nullable|exists:item_categories,id',
        ];
    }

    public function mount()
    {
        $this->authorize('read_item_categories');
        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'desc'; // Default sort direction
        $this->perPage = 100; // Default pagination limit
        $this->orderable = ['id', 'code', 'name'];

        $this->model = new ItemCategory;
        $this->initFilters($this->model);
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->authorize('create_item_categories');
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function filter()
    {
        $this->resetPage();
    }

    public function resetInputFields()
    {
        $this->code = '';
        $this->name = '';
        $this->parent_id = '';
        $this->categoryId = '';
        $this->updateMode = false;
    }

    public function store()
    {
        $this->authorize('create_item_categories');
        $this->validate();
        ItemCategory::create([
            'code' => $this->code === '' ? null : $this->code,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
        ]);
        session()->flash('message', 'Item Category created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $category = ItemCategory::findOrFail($id);
        $this->categoryId = $category->id;
        $this->code = (string) ($category->code ?? '');
        $this->name = $category->name;
        $this->parent_id = $category->parent_id;
        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->authorize('update_item_categories');
        $this->validate();
        $category = ItemCategory::findOrFail($this->categoryId);
        $category->update([
            'code' => $this->code === '' ? null : $this->code,
            'name' => $this->name,
            'parent_id' => $this->parent_id == '' ? null : $this->parent_id,
        ]);
        session()->flash('message', 'Item Category updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        $this->authorize('delete_item_categories');
        ItemCategory::find($this->deleteId)->delete();
        session()->flash('message', 'Item Category deleted successfully.');
        $this->closeModal();
    }

    public function render()
    {
        $query = ItemCategory::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%');
            });
        }

        $query = $this->applyFilters($query, $this->model);

        $categories = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $parent_categories = ItemCategory::with('children')->where('parent_id', null)->orWhere('parent_id', 0)->get();

        return view('inventory::livewire.item-categories.index', compact('categories', 'parent_categories'));
    }
}
