<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">{{ __('Shop Catalog') }}</h2>
            <p class="text-sm text-slate-500">Manage pricing and visibility for {{ $shop->name }}.</p>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('shops.index') }}" class="text-sm text-slate-600 hover:text-slate-900">? Back to shops</a>
            <a href="{{ route('shop.show', $shop) }}" class="text-sm text-emerald-600 hover:text-emerald-700">View storefront</a>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('shops.catalog.store', $shop) }}" class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            @csrf
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Product</th>
                        <th class="px-4 py-3">Base price</th>
                        <th class="px-4 py-3">Override price</th>
                        <th class="px-4 py-3">Active</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($products as $product)
                        @php
                            $shopProduct = $shopProducts->get($product->id);
                        @endphp
                        <tr class="{{ $product->active ? '' : 'opacity-60' }}">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $product->name }}</div>
                                <div class="text-xs text-slate-500">{{ $product->sku ?: 'SKU n/a' }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">${{ number_format($product->price, 2) }}</td>
                            <td class="px-4 py-3">
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="products[{{ $product->id }}][price_override]"
                                    value="{{ old('products.'.$product->id.'.price_override', optional($shopProduct)->price_override) }}"                                    
                                    class="w-32 rounded-lg border-slate-300"
                                >
                            </td>
                            <td class="px-4 py-3">
                                <input
                                    type="checkbox"
                                    name="products[{{ $product->id }}][is_active]"
                                    value="1"
                                    {{ old('products.'.$product->id.'.is_active', optional($shopProduct)->is_active ?? true) ? 'checked' : '' }}
                                    {{ $product->active ? '' : 'disabled' }}
                                >
                                <span class="ml-2 text-xs text-slate-500">{{ $product->active ? 'Visible' : 'Inactive product' }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-end border-t border-slate-200 bg-slate-50 px-4 py-3">
                <x-button>{{ __('Save Catalog') }}</x-button>
            </div>
        </form>
    </div>
</x-app-layout>
