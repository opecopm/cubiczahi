<?php

namespace App\Livewire\Customer\Shop;

use Livewire\Component;
use App\Services\CartService;
use Modules\Inventory\Models\Item;
use Modules\Selling\Models\SalesOrder;
use Modules\Selling\Models\SalesOrderItem;
use App\Models\DeliveryMethod;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class CheckoutPage extends Component
{
    public array $cartItems = [];
    public array $itemsData = [];
    public $deliveryMethodId = null;
    public $note = '';

    public function mount(CartService $cartService)
    {
        $this->cartItems = $cartService->getCart();
        
        if (empty($this->cartItems)) {
            return redirect()->route('cart.index');
        }

        $itemIds = collect($this->cartItems)->pluck('id')->unique()->toArray();
        $this->itemsData = Item::with(['prices', 'primaryImage', 'activeVariants.attribute'])
            ->whereIn('id', $itemIds)
            ->get()
            ->keyBy('id')
            ->toArray();
            
        $firstDeliveryMethod = DeliveryMethod::active()->first();
        if ($firstDeliveryMethod) {
            $this->deliveryMethodId = $firstDeliveryMethod->id;
        }
    }

    #[Computed]
    public function subtotal()
    {
        $total = 0;
        foreach ($this->cartItems as $details) {
            $unitPrice = $details['price'] ?? 0;
            $total += $unitPrice * $details['quantity'];
        }
        return $total;
    }

    #[Computed]
    public function deliveryPrice()
    {
        if (!$this->deliveryMethodId) return 0;
        $method = DeliveryMethod::find($this->deliveryMethodId);
        return $method ? $method->price : 0;
    }

    #[Computed]
    public function grandTotal()
    {
        return $this->subtotal + $this->deliveryPrice;
    }



    public function placeOrder(CartService $cartService)
    {
        $this->validate([
            'deliveryMethodId' => 'required|exists:delivery_methods,id'
        ]);

        if (empty($this->cartItems)) {
            return;
        }

        DB::beginTransaction();

        $user = auth()->user();
        $customerId = $user ? $user->ensureCustomerProfile()->id : null;

        try {
            $order = SalesOrder::create([
                'customer_id'        => $customerId,
                'status'             => 'new',
                'delivery_method_id' => $this->deliveryMethodId,
                'subtotal'           => $this->subtotal,
                'total_price'        => $this->subtotal,
                'tax'                => 0,
                'discount'           => 0,
                'delivery_fees'      => $this->deliveryPrice,
                'total'              => $this->grandTotal,
                'order_date'         => now(),
            ]);

            foreach ($this->cartItems as $details) {
                $item = $this->itemsData[$details['id']] ?? null;
                if ($item) {
                    $unitPrice = $details['price'] ?? 0;
                    $lineTotal = $unitPrice * $details['quantity'];
                    $firstVariantId = !empty($details['attributes']) ? reset($details['attributes']) : null;

                    SalesOrderItem::create([
                        'sales_order_id' => $order->id,
                        'item_id'        => $details['id'],
                        'variant_id'     => $firstVariantId,
                        'name'           => $details['name'] ?? ($item['name']['en'] ?? 'Item'),
                        'description'    => $details['description'] ?? '',
                        'unit'           => 'piece',
                        'quantity'       => $details['quantity'],
                        'price'          => $unitPrice,
                        'total_price'    => $lineTotal,
                        'subtotal'       => $lineTotal,
                        'total'          => $lineTotal,
                    ]);
                }
            }

            DB::commit();

            $cartService->clear();

            return redirect()->route('customer.order.confirm', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function render()
    {
        $deliveryMethods = DeliveryMethod::active()->get();
        return view(theme_view('livewire.customer.shop.checkout-page'), [
            'deliveryMethods' => $deliveryMethods
        ]);
    }
}
