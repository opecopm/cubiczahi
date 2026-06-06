<?php

namespace Modules\System\Livewire\MenuItems;

use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\System\Models\MenuItem;

class Index extends Component
{
    use WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public int $perPage;

    public $search = '';

    public $title;

    public $url;

    public $order;

    public $parentId;

    public $prefix;

    public $icon;

    public $menuItemId;

    public $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => [],
        'perPage' => [],
    ];

    protected function rules()
    {
        return [
            'title' => 'required|min:3|unique:menu_items,title,'.$this->menuItemId,
            'url' => 'nullable',
            'order' => 'required|integer',
            'parentId' => 'nullable|exists:menu_items,id',
            'prefix' => 'nullable',
            'icon' => 'nullable',
        ];
    }

    public function mount()
    {
        $this->sortBy = 'id'; // Default sort by ID
        $this->sortDirection = 'asc'; // Default sort direction
        $this->perPage = 100; // Default pagination limit
        $this->orderable = ['id', 'title', 'order'];
    }

    public function resetInputFields()
    {
        $this->title = '';
        $this->url = '';
        $this->order = 0;
        $this->parentId = null;
        $this->menuItemId = '';
        $this->updateMode = false;
    }

    public function filter() {}

    public function store()
    {
        $this->validate();
        MenuItem::create([
            'title' => $this->title,
            'url' => $this->url,
            'order' => $this->order,
            'parent_id' => $this->parentId,
            'prefix' => $this->prefix,
            'icon' => $this->icon,
        ]);
        session()->flash('message', 'MenuItem created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $this->menuItemId = $menuItem->id;
        $this->title = $menuItem->title;
        $this->url = $menuItem->url;
        $this->order = $menuItem->order;
        $this->parentId = $menuItem->parent_id;
        $this->prefix = $menuItem->prefix;
        $this->icon = $menuItem->icon;
        $this->updateMode = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();
        $menuItem = MenuItem::find($this->menuItemId);
        $menuItem->title = $this->title;
        $menuItem->url = $this->url;
        $menuItem->order = $this->order;
        $menuItem->parent_id = $this->parentId;
        $menuItem->prefix = $this->prefix;
        $menuItem->icon = $this->icon;
        $menuItem->save();
        session()->flash('message', 'MenuItem updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        MenuItem::find($this->deleteId)->delete();
        $this->closeModal();
        $this->deleteId = '';
        session()->flash('message', 'MenuItem deleted successfully.');
    }

    public function render()
    {
        $menuItems = MenuItem::where(function ($query) {
            $query->where('title', 'like', '%'.$this->search.'%')
                ->orWhere('url', 'like', '%'.$this->search.'%');
        })
            ->whereNull('parent_id')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $allMenuItems = MenuItem::all();

        return view('system::livewire.menu-items.index', compact('menuItems', 'allMenuItems'));
    }
}
