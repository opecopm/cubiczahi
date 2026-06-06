<?php

namespace Modules\System\Livewire\Menus;

use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\System\Models\Menu;

class Index extends Component
{
    use WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    public $name;

    public $menuId;

    public $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|min:3|unique:menus,name,'.$this->menuId,
        ];
    }

    public function mount()
    {
        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'desc'; // Default sort direction
        $this->perPage = 100; // Default pagination limit
        $this->orderable = ['id', 'name'];
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->menuId = '';
        $this->updateMode = false;
    }

    public function filter() {}

    public function store()
    {
        $this->validate();
        Menu::create(['name' => $this->name]);
        session()->flash('message', 'Menu created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        $this->menuId = $menu->id;
        $this->name = $menu->name;
        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();
        $menu = Menu::find($this->menuId);
        $menu->name = $this->name;
        $menu->save();
        session()->flash('message', 'Menu updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        Menu::find($this->deleteId)->delete();
        session()->flash('message', 'Menu deleted successfully.');
    }

    public function render()
    {
        $menus = Menu::where('name', 'like', '%'.$this->search.'%')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('system::livewire.menus.index', compact('menus'));
    }
}
