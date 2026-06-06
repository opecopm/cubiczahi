<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CartService
{
    protected const SESSION_KEY = 'store_cart';

    /**
     * Get the active DB Cart for the authenticated user, or create one.
     */
    protected function getDbCart()
    {
        $user = auth()->user();
        if (!$user) return null;

        $customerId = $user->ensureCustomerProfile()->id;

        return \Modules\Selling\Models\Cart::firstOrCreate([
            'customer_id' => $customerId,
            'status' => 'active',
            'payment_status' => 'unpaid'
        ], [
            'total_price' => 0,
            'subtotal' => 0,
            'total' => 0
        ]);
    }

    /**
     * Retrieve cart items as an array exactly matching the expected frontend structure.
     */
    public function getCart(): array
    {
        if (auth()->check()) {
            $dbCart = $this->getDbCart();
            $items = $dbCart->items()->get();

            $formattedCart = [];
            foreach ($items as $item) {
                // Determine attributes (we saved variant_id, let's reconstruct it minimally or just put it in an array)
                // Assuming we only support 1 variant right now based on our migration
                $attributes = [];
                if ($item->variant_id) {
                    $attributes[0] = (int)$item->variant_id; // Dummy attr_id just to satisfy frontend array
                }

                $cartId = $this->generateCartId($item->item_id, $attributes);
                
                $formattedCart[$cartId] = [
                    'id' => $item->item_id,
                    'quantity' => $item->quantity,
                    'attributes' => $attributes,
                    'db_id' => $item->id,
                    'price' => $item->price,
                    'name' => $item->name,
                    'description' => $item->description
                ];
            }
            return $formattedCart;
        }

        return Session::get(self::SESSION_KEY, []);
    }

    public function generateCartId(int $itemId, array $attributes): string
    {
        ksort($attributes);
        return $itemId . '_' . md5(json_encode($attributes));
    }

    /**
     * Helper to compute price and description for DB cart items
     */
    protected function buildItemDetails(int $itemId, array $attributes): array
    {
        $item = \Modules\Inventory\Models\Item::with(['prices', 'activeVariants.attribute'])->find($itemId);
        if (!$item) return ['price' => 0, 'name' => 'Unknown', 'description' => ''];

        $basePrice = collect($item->prices)->where('price_type', 'sell')->first()['price'] ?? 0;
        
        $priceDiff = 0;
        $variantId = !empty($attributes) ? reset($attributes) : null;
        
        $parts = [];
        foreach ($attributes as $attrId => $vId) {
            $variant = collect($item->activeVariants)->firstWhere('id', $vId);
            if ($variant) {
                $priceDiff += (float) $variant->price_difference;
                $parts[] = $variant->name['en'] ?? ''; // simplified
            }
        }
        
        $unitPrice = max(0, $basePrice + $priceDiff);
        
        $desc = implode(', ', array_filter($parts));
        $shortDesc = $item->short_description ?? '';
        if (is_array($shortDesc)) {
            $shortDesc = $shortDesc['en'] ?? (reset($shortDesc) ?: '');
        }

        if (empty($desc)) {
            $desc = $shortDesc;
        } else {
            if (!empty($shortDesc)) {
                $desc = $shortDesc . ' - ' . $desc;
            }
        }

        return [
            'price' => $unitPrice,
            'name' => is_array($item->name) ? ($item->name['en'] ?? 'Item') : ($item->name ?? 'Item'),
            'description' => $desc,
            'variant_id' => $variantId
        ];
    }

    public function add(int $itemId, int $quantity = 1, array $attributes = []): void
    {
        if (auth()->check()) {
            $dbCart = $this->getDbCart();
            $cartId = $this->generateCartId($itemId, $attributes);
            $existingCart = $this->getCart();

            if (isset($existingCart[$cartId])) {
                $dbItemId = $existingCart[$cartId]['db_id'];
                $cartItem = \Modules\Selling\Models\CartItem::find($dbItemId);
                if ($cartItem) {
                    $cartItem->quantity += $quantity;
                    $cartItem->total_price = $cartItem->quantity * $cartItem->price;
                    $cartItem->subtotal = $cartItem->total_price;
                    $cartItem->total = $cartItem->total_price;
                    $cartItem->save();
                }
            } else {
                $details = $this->buildItemDetails($itemId, $attributes);
                $totalPrice = $details['price'] * $quantity;

                \Modules\Selling\Models\CartItem::create([
                    'cart_id' => $dbCart->id,
                    'item_id' => $itemId,
                    'variant_id' => $details['variant_id'],
                    'name' => $details['name'],
                    'description' => $details['description'],
                    'quantity' => $quantity,
                    'price' => $details['price'],
                    'total_price' => $totalPrice,
                    'subtotal' => $totalPrice,
                    'total' => $totalPrice,
                ]);
            }
            $this->updateCartTotals($dbCart);
        } else {
            $cart = $this->getCart();
            $cartId = $this->generateCartId($itemId, $attributes);

            if (isset($cart[$cartId])) {
                $cart[$cartId]['quantity'] += $quantity;
            } else {
                $details = $this->buildItemDetails($itemId, $attributes);
                $cart[$cartId] = [
                    'id' => $itemId,
                    'quantity' => $quantity,
                    'attributes' => $attributes,
                    'price' => $details['price'],
                    'name' => $details['name'],
                    'description' => $details['description']
                ];
            }
            Session::put(self::SESSION_KEY, $cart);
        }
    }

    public function updateQuantity(string $cartId, int $quantity): void
    {
        if (auth()->check()) {
            $existingCart = $this->getCart();
            if (isset($existingCart[$cartId])) {
                $dbItemId = $existingCart[$cartId]['db_id'];
                $cartItem = \Modules\Selling\Models\CartItem::find($dbItemId);
                if ($cartItem) {
                    $cartItem->quantity = max(1, $quantity);
                    $cartItem->total_price = $cartItem->quantity * $cartItem->price;
                    $cartItem->subtotal = $cartItem->total_price;
                    $cartItem->total = $cartItem->total_price;
                    $cartItem->save();

                    $this->updateCartTotals($cartItem->cart);
                }
            }
        } else {
            $cart = $this->getCart();
            if (isset($cart[$cartId])) {
                $cart[$cartId]['quantity'] = max(1, $quantity);
                Session::put(self::SESSION_KEY, $cart);
            }
        }
    }

    public function remove(string $cartId): void
    {
        if (auth()->check()) {
            $existingCart = $this->getCart();
            if (isset($existingCart[$cartId])) {
                $dbItemId = $existingCart[$cartId]['db_id'];
                $cartItem = \Modules\Selling\Models\CartItem::find($dbItemId);
                if ($cartItem) {
                    $cart = $cartItem->cart;
                    $cartItem->delete();
                    $this->updateCartTotals($cart);
                }
            }
        } else {
            $cart = $this->getCart();
            if (isset($cart[$cartId])) {
                unset($cart[$cartId]);
                Session::put(self::SESSION_KEY, $cart);
            }
        }
    }

    public function clear(): void
    {
        if (auth()->check()) {
            $dbCart = $this->getDbCart();
            if ($dbCart) {
                $dbCart->items()->delete();
                $this->updateCartTotals($dbCart);
            }
        } else {
            Session::forget(self::SESSION_KEY);
        }
    }

    public function count(): int
    {
        $cart = $this->getCart();
        return array_sum(array_column($cart, 'quantity'));
    }

    protected function updateCartTotals($cart)
    {
        if (!$cart) return;
        $total = $cart->items()->sum('total_price');
        $cart->update([
            'total_price' => $total,
            'subtotal' => $total,
            'total' => $total
        ]);
    }

    /**
     * Merge Session Cart into DB Cart (usually called on Login)
     */
    public function mergeSessionCartToDb(): void
    {
        if (!auth()->check()) return;

        $sessionCart = Session::get(self::SESSION_KEY, []);
        if (empty($sessionCart)) return;

        $dbCart = $this->getDbCart();

        foreach ($sessionCart as $cartId => $details) {
            $itemId = $details['id'];
            $attributes = $details['attributes'] ?? [];
            $variantId = !empty($attributes) ? reset($attributes) : null;

            // Delete existing matching DB item so it is overridden by the session
            $query = \Modules\Selling\Models\CartItem::where('cart_id', $dbCart->id)
                ->where('item_id', $itemId);
                
            if ($variantId) {
                $query->where('variant_id', $variantId);
            } else {
                $query->whereNull('variant_id');
            }
            $query->delete();

            $this->add($itemId, $details['quantity'], $attributes);
        }

        Session::forget(self::SESSION_KEY);
    }
}
