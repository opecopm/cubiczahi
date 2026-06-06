<?php

namespace App\Livewire\Customer\Catalog;

use Livewire\Component;
use App\Services\CartService;
use Modules\Inventory\Models\Item;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class AddToCart extends Component
{
    public Item $item;
    public int $quantity = 1;
    
    #[Url]
    public array $selectedAttributes = [];
    public bool $compact = false;
    public function mount(Item $item, bool $compact = false)
    {
        $this->compact = $compact;
        $this->item = $item->load('activeVariants.attribute', 'prices', 'primaryImage', 'media');
        
        foreach ($this->item->activeVariants->groupBy('attribute_id') as $attrId => $variants) {
            $default = $variants->firstWhere('is_default', true) ?? $variants->first();
            if ($default && !isset($this->selectedAttributes[$attrId])) {
                $this->selectedAttributes[$attrId] = (int) $default->id;
            }
        }
    }

    public function selectAttribute(int $attributeId, int $variantId)
    {
        $this->selectedAttributes[$attributeId] = $variantId;
    }

    public function increment()
    {
        $this->quantity++;
    }

    public function decrement()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }
    
    #[Computed]
    public function unitPrice(): float
    {
        $basePrice = (float) ($this->item->prices->where('price_type', 'sell')->first()?->price ?? 0);

        foreach ($this->selectedAttributes as $variantId) {
            $variant = $this->item->activeVariants->firstWhere('id', $variantId);
            if ($variant) {
                $basePrice += (float) $variant->price_difference;
            }
        }

        return max(0, $basePrice);
    }
    
    #[Computed]
    public function totalPrice(): float
    {
        return $this->unitPrice * $this->quantity;
    }

    public function addToCart(CartService $cartService)
    {
        $cartService->add($this->item->id, $this->quantity, $this->selectedAttributes);
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view(theme_view('livewire.customer.catalog.add-to-cart'));
    }
}
