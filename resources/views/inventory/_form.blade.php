@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="product_id" class="block text-sm font-medium text-slate-700">Product</label>
        <select id="product_id" name="product_id" required class="mt-1 w-full rounded-md border-slate-300">
            <option value="">Select a product</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected((string) old('product_id', $inventory->product_id ?? '') === (string) $product->id)>{{ $product->name }}</option>
            @endforeach
        </select>
        @error('product_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="warehouse_id" class="block text-sm font-medium text-slate-700">Warehouse</label>
        <select id="warehouse_id" name="warehouse_id" required class="mt-1 w-full rounded-md border-slate-300">
            <option value="">Select a warehouse</option>
            @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @selected((string) old('warehouse_id', $inventory->warehouse_id ?? '') === (string) $warehouse->id)>{{ $warehouse->name }}</option>
            @endforeach
        </select>
        @error('warehouse_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="quantity" class="block text-sm font-medium text-slate-700">Quantity</label>
        <input id="quantity" name="quantity" type="number" min="0" step="0.0001" required value="{{ old('quantity', $inventory->quantity ?? '0') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="reserved" class="block text-sm font-medium text-slate-700">Reserved</label>
        <input id="reserved" name="reserved" type="number" min="0" step="0.0001" required value="{{ old('reserved', $inventory->reserved ?? '0') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('reserved') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="reorder_point" class="block text-sm font-medium text-slate-700">Reorder point</label>
        <input id="reorder_point" name="reorder_point" type="number" min="0" step="0.0001" required value="{{ old('reorder_point', $inventory->reorder_point ?? '0') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('reorder_point') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        Save
    </button>
    <a href="{{ route('inventory.list') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
</div>
