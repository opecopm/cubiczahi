<?php

namespace Modules\CMS\Livewire\Menus;

use Livewire\Component;
use Modules\CMS\Models\Menu;
use Modules\CMS\Models\MenuItem;

class Show extends Component
{
    public $menu;
    public $menuItems;
    public $selectedMenuItems = [];

    public function mount($id)
    {
        $this->menu = Menu::find($id);
        $this->menuItems = MenuItem::where('parent_id', null)->get();
        $this->selectedMenuItems = $this->menu->items->pluck('id')->toArray(); // Pre-select existing menu items
    }

    public function addMenuItems()
    {
        $this->menu->items()->sync($this->selectedMenuItems);
        session()->flash('message', 'Menu items added successfully.');
        // $this->reset('selectedMenuItems'); // Clear the selection
    }

    public function render()
    {
        return view('cms::livewire.menus.show', [
            'menu' => $this->menu,
            'menuItems' => $this->menuItems,
        ]);
    }
}
