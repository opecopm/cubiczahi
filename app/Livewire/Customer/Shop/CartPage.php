<?php

namespace App\Livewire\Customer\Shop;

use Livewire\Component;
use App\Services\CartService;
use Modules\Inventory\Models\Item;

class CartPage extends Component
{
    public array $cartItems = [];
    public $itemsData = [];

    public function mount(CartService $cartService)
    {
        $this->cartItems = $cartService->getCart();
        $this->loadItemsData();
    }

    public function loadItemsData()
    {
        $itemIds = collect($this->cartItems)->pluck('id')->unique()->toArray();
        $this->itemsData = Item::with(['prices', 'primaryImage', 'activeVariants.attribute'])
            ->whereIn('id', $itemIds)
            ->get()
            ->keyBy('id');
    }

    public function removeItem(string $cartId, CartService $cartService)
    {
        $cartService->remove($cartId);
        $this->cartItems = $cartService->getCart();
        $this->dispatch('cart-updated');
    }

    public function increment(string $cartId, CartService $cartService)
    {
        if (isset($this->cartItems[$cartId])) {
            $this->cartItems[$cartId]['quantity']++;
            $cartService->updateQuantity($cartId, $this->cartItems[$cartId]['quantity']);
            $this->dispatch('cart-updated');
        }
    }

    public function decrement(string $cartId, CartService $cartService)
    {
        if (isset($this->cartItems[$cartId]) && $this->cartItems[$cartId]['quantity'] > 1) {
            $this->cartItems[$cartId]['quantity']--;
            $cartService->updateQuantity($cartId, $this->cartItems[$cartId]['quantity']);
            $this->dispatch('cart-updated');
        }
    }

    public function render()
    {
        $subtotal = 0;
        foreach ($this->cartItems as $cartId => $details) {
            $unitPrice = $details['price'] ?? 0;
            $subtotal += $unitPrice * $details['quantity'];
        }
        
        return view(theme_view('livewire.customer.shop.cart-page'), [
            'subtotal' => $subtotal
        ]);
    }
}
