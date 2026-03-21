<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShopController extends Controller
{
    public function index()
    {
        return view('shops.index', [
            'shops' => Shop::query()
                ->with(['owner', 'warehouse'])
                ->orderByDesc('created_at')
                ->paginate(15),
        ]);
    }

    public function create()
    {
        return view('shops.create', [
            'owners' => User::query()->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:shops,slug'],
            'description' => ['nullable', 'string'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Shop::generateUniqueSlug($validated['name']);
        }

        Shop::create($validated);

        return redirect()->route('shops.index')->with('status', 'Shop created successfully.');
    }

    public function edit(Shop $shop)
    {
        return view('shops.edit', [
            'shop' => $shop,
            'owners' => User::query()->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('shops', 'slug')->ignore($shop->id)],
            'description' => ['nullable', 'string'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Shop::generateUniqueSlug($validated['name']);
        }

        $shop->update($validated);

        return redirect()->route('shops.index')->with('status', 'Shop updated successfully.');
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();

        return redirect()->route('shops.index')->with('status', 'Shop deleted successfully.');
    }
}
