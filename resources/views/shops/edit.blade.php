<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">{{ __('Edit Shop') }}</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('shops.update', $shop) }}" class="space-y-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PATCH')

            <div>
                <x-label for="name" :value="__('Name')" />
                <x-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $shop->name) }}" required />
            </div>

            <div>
                <x-label for="slug" :value="__('Slug')" />
                <x-input id="slug" name="slug" type="text" class="mt-1 block w-full" value="{{ old('slug', $shop->slug) }}" />
            </div>

            <div>
                <x-label for="description" :value="__('Description')" />
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border-slate-300">{{ old('description', $shop->description) }}</textarea>
            </div>

            <div>
                <x-label for="owner_id" :value="__('Owner')" />
                <select id="owner_id" name="owner_id" class="mt-1 block w-full rounded-lg border-slate-300">
                    <option value="">No owner</option>
                    @foreach ($owners as $owner)
                        <option value="{{ $owner->id }}" {{ (string) old('owner_id', $shop->owner_id) === (string) $owner->id ? 'selected' : '' }}>
                            {{ $owner->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-label for="warehouse_id" :value="__('Warehouse')" />
                <select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full rounded-lg border-slate-300">
                    <option value="">No warehouse</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ (string) old('warehouse_id', $shop->warehouse_id) === (string) $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-label for="is_active" :value="__('Status')" />
                <select id="is_active" name="is_active" class="mt-1 block w-full rounded-lg border-slate-300">
                    <option value="1" {{ old('is_active', $shop->is_active) ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ ! old('is_active', $shop->is_active) ? 'selected' : '' }}>Paused</option>
                </select>
            </div>

            <div class="flex justify-end">
                <x-button>{{ __('Save Changes') }}</x-button>
            </div>
        </form>
    </div>
</x-app-layout>
