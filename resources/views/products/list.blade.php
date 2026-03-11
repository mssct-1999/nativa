<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Products List') }}
            </h2>
            <a href="{{ route('products.create') }}" class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                {{ __('Add new product') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4">
        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="GET" action="{{ route('products.list') }}" class="flex flex-wrap items-center gap-3">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="{{ __('Search products, SKU, barcode...') }}"
                class="w-full max-w-md rounded-md border-slate-300 text-sm"
            />
            <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                {{ __('Search') }}
            </button>
            @if (!empty($search))
                <a href="{{ route('products.list') }}" class="text-sm text-slate-600 hover:text-slate-900">{{ __('Clear') }}</a>
            @endif
        </form>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ __('SKU') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Barcode Graphic') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Name') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Price') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Active') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <tr>
                            <td class="px-4 py-3">{{ $product->sku ?: '-' }}</td>
                            <td class="px-4 py-3">
                                @if($product->barcode && strlen($product->barcode) == 13 && ctype_digit($product->barcode))
                                    <svg id="barcode-{{ $product->id }}" style="max-width: 150px;"></svg>
                                    <script>
                                        try {
                                            JsBarcode("#barcode-{{ $product->id }}", "{{ $product->barcode }}", {
                                                format: "EAN13",
                                                width: 1.5,
                                                height: 40,
                                                displayValue: true,
                                                fontSize: 10,
                                                valid: function() { return true; }
                                            });
                                        } catch (error) {
                                            console.error("Barcode error for product {{ $product->id }}:", error);
                                            document.getElementById("barcode-{{ $product->id }}").innerHTML = '<text x="0" y="20" font-size="12" fill="red">Invalid barcode</text>';
                                        }
                                    </script>
                                @else
                                    @if($product->barcode)
                                        <span class="text-xs text-red-600" title="{{ __('Invalid barcode format') }}">{{ substr($product->barcode, 0, 20) }}...</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $product->name }}</td>
                            <td class="px-4 py-3">${{ number_format((float) $product->price, 2) }}</td>
                            <td class="px-4 py-3">{{ $product->active ? __('Yes') : __('No') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('products.show', $product) }}" class="text-slate-700 hover:text-slate-900 font-medium">{{ __('Details') }}</a>
                                    <a href="{{ route('products.edit', $product) }}" class="text-amber-600 hover:text-amber-700 font-medium">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('{{ __('Delete this product?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 font-medium">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">{{ __('No products found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $products->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
