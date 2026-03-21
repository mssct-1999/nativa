<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Http\Request;

class ShopCatalogController extends Controller
{
    public function index(Shop $shop)
    {
        $products = Product::query()
            ->orderBy('name')
            ->get();

        $shopProducts = ShopProduct::query()
            ->where('shop_id', $shop->id)
            ->get()
            ->keyBy('product_id');

        return view('shops.catalog', [
            'shop' => $shop,
            'products' => $products,
            'shopProducts' => $shopProducts,
        ]);
    }

    public function store(Request $request, Shop $shop)
    {
        $payload = $request->input('products', []);

        if (! is_array($payload)) {
            return redirect()->route('shops.catalog', $shop)->with('status', 'No products provided.');
        }

        $productIds = array_map('intval', array_keys($payload));
        $existingProducts = Product::query()->whereIn('id', $productIds)->pluck('id')->all();
        $existingLookup = array_flip($existingProducts);

        foreach ($payload as $productId => $data) {
            $productId = (int) $productId;

            if (! isset($existingLookup[$productId])) {
                continue;
            }

            $priceOverride = $data['price_override'] ?? null;
            $priceOverride = $priceOverride === '' ? null : $priceOverride;
            $isActive = isset($data['is_active']) && (bool) $data['is_active'];

            ShopProduct::updateOrCreate(
                ['shop_id' => $shop->id, 'product_id' => $productId],
                [
                    'price_override' => $priceOverride !== null ? (float) $priceOverride : null,
                    'is_active' => $isActive,
                ]
            );
        }

        return redirect()->route('shops.catalog', $shop)->with('status', 'Catalog updated successfully.');
    }
}
