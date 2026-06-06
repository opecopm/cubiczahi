<?php

namespace App\Livewire\Layout;

use Livewire\Component;
use App\Services\CartService;
use Livewire\Attributes\On;

class CartCounter extends Component
{
    public int $count = 0;

    public function mount(CartService $cartService)
    {
        $this->count = $cartService->count();
    }

    #[On('cart-updated')]
    public function updateCount(CartService $cartService)
    {
        $this->count = $cartService->count();
    }

    public function render()
    {
        return view(theme_view('livewire.layout.cart-counter'));
    }
}
