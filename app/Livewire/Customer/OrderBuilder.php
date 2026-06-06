<?php

namespace App\Livewire\Customer;

use App\Models\DeliveryMethod;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCategory;
use Modules\Selling\Models\SalesOrder;
use Modules\Selling\Models\SalesOrderItem;
use Modules\Selling\Notifications\NewOrderNotification;
use Spatie\Permission\Models\Role;

use function system_setting;
use function theme_view;

class OrderBuilder extends Component
{
    public array $cart = [];  // [itemId => ['qty' => 1, 'attributes' => [attrId => variantId, ...]]]

    public string $activeCategory = '';

    public string $search = '';

    public string $note = '';

    public ?int $deliveryMethodId = null;

    public bool $showCart = false;

    public bool $showReviewModal = false;

    // ── Cart mutations ─────────────────────────────────────

    public function add(int $id): void
    {
        if (isset($this->cart[$id])) {
            return;
        }

        $service = Item::with(['activeVariants'])->find($id);

        // Pre-fill with the default variant per attribute group
        $attributes = [];
        foreach ($service?->activeVariants->groupBy('attribute_id') ?? [] as $attrId => $attrVariants) {
            $default = $attrVariants->firstWhere('is_default', true) ?? $attrVariants->first();
            if ($default) {
                $attributes[$attrId] = $default->id;
            }
        }

        $this->cart[$id] = [
            'qty' => 1,
            'attributes' => $attributes,
        ];
    }

    public function remove(int $id): void
    {
        unset($this->cart[$id]);
        $this->cart = $this->cart;
    }

    public function increment(int $id): void
    {
        $this->cart[$id]['qty'] = min(($this->cart[$id]['qty'] ?? 1) + 1, 99);
    }

    public function decrement(int $id): void
    {
        if (($this->cart[$id]['qty'] ?? 1) <= 1) {
            $this->remove($id);

            return;
        }
        $this->cart[$id]['qty']--;
    }

    public function selectAttribute(int $itemId, int $attributeId, int $variantId): void
    {
        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]['attributes'][$attributeId] = $variantId;
            $this->cart = $this->cart;
        }
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->note = '';
    }

    // ── Computed helpers ───────────────────────────────────

    #[Computed]
    public function cartServices(): Collection
    {
        if (empty($this->cart)) {
            return collect();
        }

        return Item::whereIn('id', array_keys($this->cart))
            ->with([
                'primaryImage',
                'prices',
                'activeVariants.attribute',
            ])
            ->get()
            ->keyBy('id');
    }

    #[Computed]
    public function cartCount(): int
    {
        return array_sum(array_column($this->cart, 'qty'));
    }

    #[Computed]
    public function subtotal(): float
    {
        $total = 0.0;
        foreach ($this->cart as $id => $entry) {
            $service = $this->cartServices->get($id);
            if (! $service) {
                continue;
            }
            $price = $this->resolvePrice($service, $entry['attributes'] ?? []);
            $total += $price * $entry['qty'];
        }

        return $total;
    }

    #[Computed]
    public function selectedDelivery(): ?DeliveryMethod
    {
        if (! $this->deliveryMethodId) {
            return null;
        }

        return DeliveryMethod::find($this->deliveryMethodId);
    }

    #[Computed]
    public function deliveryPrice(): float
    {
        return (float) ($this->selectedDelivery?->price ?? 0);
    }

    #[Computed]
    public function grandTotal(): float
    {
        return $this->subtotal + $this->deliveryPrice;
    }

    // ── Place order ────────────────────────────────────────

    public function placeOrder(): void
    {
        if (empty($this->cart)) {
            return;
        }

        $currency = system_setting('default_currency', 'SAR');
        $services = $this->cartServices;
        /** @var User $user */
        $user     = Auth::user();
        $customer = $user->ensureCustomerProfile();

        $order = DB::transaction(function () use ($user, $customer, $currency, $services) {
            $order = SalesOrder::create([
                'reference'          => null,
                'customer_id'        => $customer->id,
                'currency'           => $currency,
                'currency_rate'      => 1,
                'status'             => 'draft',
                'order_date'         => now()->toDateString(),
                'subtotal'           => $this->subtotal,
                'delivery_fees'      => $this->deliveryPrice,
                'total_price'        => $this->grandTotal,
                'total'              => $this->grandTotal,
                'delivery_method_id' => $this->deliveryMethodId,
                'created_by'         => $user->id,
            ]);

            foreach ($this->cart as $id => $entry) {
                $service = $services->get($id);
                if (! $service) {
                    continue;
                }

                $attributeIds = $entry['attributes'] ?? [];
                $price        = $this->resolvePrice($service, $attributeIds);
                $lineTotal    = $price * $entry['qty'];

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'item_id'        => $id,
                    'name'           => $service->getTranslation('name', 'en'),
                    'description'    => $this->buildAttributeDescription($service, $attributeIds),
                    'quantity'       => $entry['qty'],
                    'price'          => $price,
                    'total_price'    => $lineTotal,
                    'subtotal'       => $lineTotal,
                    'total'          => $lineTotal,
                ]);
            }

            return $order;
        });

        // Notify all Admins and Cashiers via DB + email
        $order->load(['customer', 'deliveryMethod']);

        $existingRoles = Role::whereIn('name', ['Admin', 'Cashier'])
            ->where('guard_name', 'web')
            ->pluck('name')
            ->toArray();

        if (! empty($existingRoles)) {
            User::role($existingRoles)->get()
                ->each(fn (User $recipient) => $recipient->notify(new NewOrderNotification($order)));
        }

        $this->clearCart();

        session()->flash('order_placed', $order->reference ?? $order->id);
        $this->redirect(route('customer.order.confirm', $order->id), navigate: true);
    }

    // ── Render ─────────────────────────────────────────────

    public function render(): View
    {
        $services = Item::where('status', 'active')
            ->where('type', 'service')
            ->with([
                'primaryImage',
                'prices',
                'category',
                'activeVariants.attribute',
            ])
            ->when($this->activeCategory, fn ($q) => $q->where('category_id', $this->activeCategory))
            ->when($this->search, fn ($q) => $q->where('name->en', 'like', '%'.$this->search.'%'))
            ->orderBy('name->en')
            ->get();

        $categories = ItemCategory::withCount(['items' => fn ($q) => $q->where('status', 'active')->where('type', 'service')])
            ->whereHas('items', fn ($q) => $q->where('status', 'active')->where('type', 'service'))
            ->get();

        $deliveryMethods = DeliveryMethod::active()->get();

        // Auto-select first method on first load
        if ($this->deliveryMethodId === null && $deliveryMethods->isNotEmpty()) {
            $this->deliveryMethodId = $deliveryMethods->first()->id;
        }

        return view(theme_view('livewire.customer.order-builder'), [
            'services' => $services,
            'categories' => $categories,
            'deliveryMethods' => $deliveryMethods,
        ])->layout(theme_view('layouts.guest'));
    }

    // ── Helpers ────────────────────────────────────────────

    /**
     * Calculate price: base item price + sum of selected variant price_differences
     */
    private function resolvePrice(Item $service, array $attributeIds): float
    {
        $basePrice = (float) ($service->prices->where('price_type', 'sell')->first()?->price ?? 0);

        // Add price differences from selected variants
        foreach ($attributeIds as $variantId) {
            $variant = $service->activeVariants->firstWhere('id', $variantId);
            if ($variant) {
                $basePrice += (float) $variant->price_difference;
            }
        }

        return max(0, $basePrice);
    }

    /**
     * Build a human-readable description from selected attribute options
     * e.g. "Wash + Iron, Express (24h), Delicate Fabric"
     */
    private function buildAttributeDescription(Item $service, array $attributeIds): string
    {
        $parts = [];

        foreach ($attributeIds as $variantId) {
            $variant = $service->activeVariants->firstWhere('id', $variantId);
            if ($variant) {
                $parts[] = $variant->getTranslation('name', 'en');
            }
        }

        return implode(', ', $parts) ?: null;
    }
}
