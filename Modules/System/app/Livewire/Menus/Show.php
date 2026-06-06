<?php

namespace Modules\System\Livewire\Menus;

use Livewire\Component;
use Modules\System\Models\Menu;
use Modules\System\Models\MenuItem;

class Show extends Component
{
    public $menu;

    public $menuItems;

    public $selectedMenuItems = [];

    public function mount(Menu $menu)
    {
        $this->menu = $menu;
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
        return view('system::livewire.menus.show', [
            'menu' => $this->menu,
            'menuItems' => $this->menuItems,
        ]);
    }
}
