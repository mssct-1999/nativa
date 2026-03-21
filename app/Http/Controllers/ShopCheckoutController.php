<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shop;
use App\Models\ShopCustomer;
use App\Models\ShopProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ShopCheckoutController extends Controller
{
    public function create(Request $request, Shop $shop)
    {
        if (! $shop->is_active) {
            abort(404);
        }

        $cart = session()->get($this->cartKey($shop), []);

        if (empty($cart)) {
            return redirect()->route('shop.show', $shop)->with('status', 'Your cart is empty.');
        }

        $customer = ShopCustomer::firstOrCreate(['user_id' => $request->user()->id]);
        $shopProducts = $this->loadShopProducts($shop, $cart);

        if ($shopProducts->count() !== count($cart)) {
            return redirect()->route('shop.cart', $shop)->with('status', 'Some items are no longer available.');
        }

        $summary = $this->cartSummary($cart, $customer, $shopProducts);

        $items = collect($cart)->map(function ($item) use ($shopProducts) {
            $shopProduct = $shopProducts->get($item['product_id']);
            if ($shopProduct) {
                $item['price'] = $shopProduct->effectivePrice();
            }

            return $item;
        })->values()->all();

        return view('shop.checkout', [
            'shop' => $shop,
            'items' => $items,
            'summary' => $summary,
            'customer' => $customer,
        ]);
    }

    public function store(Request $request, Shop $shop)
    {
        if (! $shop->is_active) {
            abort(404);
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if (! $shop->warehouse_id) {
            throw ValidationException::withMessages([
                'payment_method' => 'Shop warehouse is not configured.',
            ]);
        }

        $cart = session()->get($this->cartKey($shop), []);

        if (empty($cart)) {
            return redirect()->route('shop.show', $shop)->with('status', 'Your cart is empty.');
        }

        $productIds = array_map('intval', array_keys($cart));
        $shopProducts = $this->loadShopProducts($shop, $cart);

        if ($shopProducts->count() !== count($productIds)) {
            return redirect()->route('shop.cart', $shop)->with('status', 'Some items are no longer available.');
        }

        $customer = ShopCustomer::firstOrCreate(['user_id' => $request->user()->id]);
        $summary = $this->cartSummary($cart, $customer, $shopProducts);

        $order = DB::transaction(function () use ($validated, $shop, $cart, $shopProducts, $customer, $summary, $request) {
            $order = Order::create([
                'number' => $this->generateOrderNumber(),
                'client_id' => null,
                'shop_id' => $shop->id,
                'shop_customer_id' => $customer->id,
                'created_by' => null,
                'status' => 'completed',
                'channel' => 'online',
                'total' => $summary['total'],
                'discount' => $summary['discount'],
                'ordered_at' => now(),
                'paid_at' => now(),
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($cart as $item) {
                $shopProduct = $shopProducts->get($item['product_id']);
                $product = $shopProduct->product;
                $quantity = (float) $item['quantity'];
                $unitPrice = $shopProduct->effectivePrice();
                $lineTotal = $unitPrice * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'description' => $product->description,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $lineTotal,
                ]);

                $inventory = Inventory::firstOrCreate(
                    ['product_id' => $product->id, 'warehouse_id' => $shop->warehouse_id],
                    ['quantity' => 0, 'reserved' => 0, 'reorder_point' => 0]
                );

                $available = (float) $inventory->quantity - (float) $inventory->reserved;

                if ($available < $quantity) {
                    throw ValidationException::withMessages([
                        'payment_method' => 'Not enough stock for '.$product->name.'.',
                    ]);
                }

                $inventory->quantity = (float) $inventory->quantity - $quantity;
                $inventory->save();

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'warehouse_from' => $shop->warehouse_id,
                    'warehouse_to' => null,
                    'quantity' => $quantity,
                    'type' => 'out',
                    'reference_type' => 'orders',
                    'reference_id' => $order->id,
                    'user_id' => $request->user()->id,
                    'notes' => 'Online order '.$order->number.' payment confirmed.',
                ]);
            }

            $customer->registerOrder($summary['total']);

            return $order;
        });

        session()->forget($this->cartKey($shop));

        return redirect()->route('shop.show', $shop)->with('status', 'Order '.$order->number.' placed successfully.');
    }

    private function cartKey(Shop $shop): string
    {
        return 'shop_cart_'.$shop->id;
    }

    private function cartSummary(array $cart, ShopCustomer $customer, $shopProducts): array
    {
        $subtotal = 0;

        foreach ($cart as $item) {
            $shopProduct = $shopProducts->get($item['product_id']);
            $unitPrice = $shopProduct ? $shopProduct->effectivePrice() : (float) $item['price'];
            $subtotal += $unitPrice * (float) $item['quantity'];
        }

        $discountRate = (float) $customer->discount_rate;
        $discount = $subtotal * ($discountRate / 100);
        $total = $subtotal - $discount;

        return [
            'subtotal' => $subtotal,
            'discount_rate' => $discountRate,
            'discount' => $discount,
            'total' => $total,
        ];
    }

    private function loadShopProducts(Shop $shop, array $cart)
    {
        $productIds = array_map('intval', array_keys($cart));

        return ShopProduct::query()
            ->with('product')
            ->where('shop_id', $shop->id)
            ->whereIn('product_id', $productIds)
            ->where('is_active', true)
            ->whereHas('product', function ($query) {
                $query->where('active', true);
            })
            ->get()
            ->keyBy('product_id');
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'W'.Str::upper(Str::random(8));
        } while (Order::query()->where('number', $number)->exists());

        return $number;
    }
}
