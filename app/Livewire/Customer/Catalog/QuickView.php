<?php

namespace App\Livewire\Customer\Catalog;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\Inventory\Models\Item;

class QuickView extends Component
{
    public ?Item $item = null;
    public bool $isOpen = false;

    #[On('openQuickView')]
    public function loadItem($itemId)
    {
        $this->item = Item::with('activeVariants.attribute', 'prices', 'primaryImage', 'media')->find($itemId);
        $this->isOpen = true;
    }

    public function render()
    {
        return view(theme_view('livewire.customer.catalog.quick-view'));
    }
}
