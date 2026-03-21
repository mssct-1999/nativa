<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Http\Request;

class ShopCartController extends Controller
{
    public function index(Shop $shop)
    {
        if (! $shop->is_active) {
            abort(404);
        }

        return view('shop.cart', [
            'shop' => $shop,
            'items' => $this->cartItems($shop),
            'summary' => $this->cartSummary($shop),
        ]);
    }

    public function store(Request $request, Shop $shop)
    {
        if (! $shop->is_active) {
            abort(404);
        }

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (! $product->active || ! $shop->is_active) {
            return redirect()->route('shop.show', $shop)->with('status', 'Product not available.');
        }

        $shopProduct = ShopProduct::query()
            ->where('shop_id', $shop->id)
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->first();

        if (! $shopProduct) {
            return redirect()->route('shop.show', $shop)->with('status', 'Product not available in this shop.');
        }

        $available = $this->availableStock($shop, $product->id);
        $quantity = (float) $validated['quantity'];

        if ($available !== null && $quantity > $available) {
            return redirect()->route('shop.show', $shop)->with('status', 'Not enough stock available.');
        }

        $cart = $this->cart($shop);

        if (! isset($cart[$product->id])) {
            $cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $shopProduct->effectivePrice(),
                'quantity' => 0,
            ];
        }

        $desired = $cart[$product->id]['quantity'] + $quantity;

        if ($available !== null && $desired > $available) {
            return redirect()->route('shop.show', $shop)->with('status', 'Not enough stock available.');
        }

        $cart[$product->id]['quantity'] = $desired;
        $this->storeCart($shop, $cart);

        return redirect()->route('shop.cart', $shop)->with('status', 'Product added to cart.');
    }

    public function update(Request $request, Shop $shop)
    {
        if (! $shop->is_active) {
            abort(404);
        }

        $payload = $request->input('items', []);

        if (! is_array($payload)) {
            return redirect()->route('shop.cart', $shop)->with('status', 'Cart update failed.');
        }

        $cart = $this->cart($shop);

        foreach ($payload as $productId => $data) {
            $productId = (int) $productId;
            if (! isset($cart[$productId])) {
                continue;
            }

            $quantity = isset($data['quantity']) ? (float) $data['quantity'] : 0;

            if ($quantity <= 0) {
                unset($cart[$productId]);
                continue;
            }

            $available = $this->availableStock($shop, $productId);
            if ($available !== null && $quantity > $available) {
                $quantity = $available;
            }

            if ($quantity <= 0) {
                unset($cart[$productId]);
                continue;
            }

            $cart[$productId]['quantity'] = $quantity;
        }

        $this->storeCart($shop, $cart);

        return redirect()->route('shop.cart', $shop)->with('status', 'Cart updated.');
    }

    public function destroy(Shop $shop, Product $product)
    {
        if (! $shop->is_active) {
            abort(404);
        }

        $cart = $this->cart($shop);
        unset($cart[$product->id]);
        $this->storeCart($shop, $cart);

        return redirect()->route('shop.cart', $shop)->with('status', 'Product removed.');
    }

    private function cartKey(Shop $shop): string
    {
        return 'shop_cart_'.$shop->id;
    }

    private function cart(Shop $shop): array
    {
        return session()->get($this->cartKey($shop), []);
    }

    private function storeCart(Shop $shop, array $cart): void
    {
        session()->put($this->cartKey($shop), $cart);
    }

    private function cartItems(Shop $shop): array
    {
        return array_values($this->cart($shop));
    }

    private function cartSummary(Shop $shop): array
    {
        $items = $this->cart($shop);
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += (float) $item['price'] * (float) $item['quantity'];
        }

        return [
            'subtotal' => $subtotal,
            'items_count' => count($items),
        ];
    }

    private function availableStock(Shop $shop, int $productId): ?float
    {
        if (! $shop->warehouse_id) {
            return null;
        }

        $inventory = Inventory::query()
            ->where('warehouse_id', $shop->warehouse_id)
            ->where('product_id', $productId)
            ->first();

        if (! $inventory) {
            return 0;
        }

        return (float) $inventory->quantity - (float) $inventory->reserved;
    }
}
