@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="sku" class="block text-sm font-medium text-slate-700">SKU</label>
        <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('sku') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="name" class="block text-sm font-medium text-slate-700">Name</label>
        <input id="name" name="name" type="text" required value="{{ old('name', $product->name ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="price" class="block text-sm font-medium text-slate-700">Price</label>
        <input id="price" name="price" type="number" step="0.01" min="0" required value="{{ old('price', $product->price ?? '0') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="cost" class="block text-sm font-medium text-slate-700">Cost</label>
        <input id="cost" name="cost" type="number" step="0.01" min="0" required value="{{ old('cost', $product->cost ?? '0') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('cost') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-4">
    <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
    <textarea id="description" name="description" rows="4" class="mt-1 w-full rounded-md border-slate-300">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div class="mt-4 flex items-center gap-6">
    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
        <input type="checkbox" name="taxable" value="1" class="rounded border-slate-300" @checked(old('taxable', $product->taxable ?? true)) />
        Taxable
    </label>
    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
        <input type="checkbox" name="active" value="1" class="rounded border-slate-300" @checked(old('active', $product->active ?? true)) />
        Active
    </label>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        Save
    </button>
    <a href="{{ route('products.list') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
</div>
