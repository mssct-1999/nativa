<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;

class StorefrontController extends Controller
{
    public function index()
    {
        return view('shop.index', [
            'shops' => Shop::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function show(Shop $shop)
    {
        if (! $shop->is_active) {
            abort(404);
        }

        $shopProducts = ShopProduct::query()
            ->with('product')
            ->where('shop_id', $shop->id)
            ->where('is_active', true)
            ->whereHas('product', function ($query) {
                $query->where('active', true);
            })
            ->orderBy('id')
            ->get();

        $warehouseId = $shop->warehouse_id;
        $inventoryMap = collect();

        if ($warehouseId) {
            $inventoryMap = Inventory::query()
                ->where('warehouse_id', $warehouseId)
                ->whereIn('product_id', $shopProducts->pluck('product_id'))
                ->get()
                ->keyBy('product_id');
        }

        return view('shop.show', [
            'shop' => $shop,
            'shopProducts' => $shopProducts,
            'inventoryMap' => $inventoryMap,
        ]);
    }

    public function product(Shop $shop, Product $product)
    {
        if (! $shop->is_active || ! $product->active) {
            abort(404);
        }

        $shopProduct = ShopProduct::query()
            ->where('shop_id', $shop->id)
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->firstOrFail();

        $inventory = null;
        if ($shop->warehouse_id) {
            $inventory = Inventory::query()
                ->where('warehouse_id', $shop->warehouse_id)
                ->where('product_id', $product->id)
                ->first();
        }

        return view('shop.product', [
            'shop' => $shop,
            'product' => $product,
            'shopProduct' => $shopProduct,
            'inventory' => $inventory,
        ]);
    }
}
