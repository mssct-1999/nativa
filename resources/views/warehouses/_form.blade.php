@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="name" class="block text-sm font-medium text-slate-700">Name</label>
        <input id="name" name="name" type="text" required value="{{ old('name', $warehouse->name ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="code" class="block text-sm font-medium text-slate-700">Code</label>
        <input id="code" name="code" type="text" value="{{ old('code', $warehouse->code ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="location" class="block text-sm font-medium text-slate-700">Location</label>
        <input id="location" name="location" type="text" value="{{ old('location', $warehouse->location ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="contact" class="block text-sm font-medium text-slate-700">Contact</label>
        <input id="contact" name="contact" type="text" value="{{ old('contact', $warehouse->contact ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('contact') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-4">
    <label for="notes" class="block text-sm font-medium text-slate-700">Notes</label>
    <textarea id="notes" name="notes" rows="4" class="mt-1 w-full rounded-md border-slate-300">{{ old('notes', $warehouse->notes ?? '') }}</textarea>
    @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        Save
    </button>
    <a href="{{ route('warehouses.list') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
</div>
