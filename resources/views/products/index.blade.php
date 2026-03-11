<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <x-index-overview :description="$pageDescription" :metrics="$metrics" :chart="$chart" :list-route="route('products.list')" />

        <section class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">{{ __('Top sold products') }}</h3>
                    <p class="text-sm text-slate-500">{{ __('Based on sales in the last :days days.', ['days' => $topSoldRangeDays]) }}</p>
                </div>
                <a href="{{ route('products.list') }}" class="text-sm text-slate-600 hover:text-slate-900">{{ __('View all') }}</a>
            </div>

            @if ($topSold->isEmpty())
                <p class="mt-4 text-sm text-slate-500">{{ __('No sales data available yet.') }}</p>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 text-left">{{ __('Product') }}</th>
                                <th class="px-4 py-3 text-left">{{ __('SKU') }}</th>
                                <th class="px-4 py-3 text-left">{{ __('Barcode') }}</th>
                                <th class="px-4 py-3 text-left">{{ __('Sold qty') }}</th>
                                <th class="px-4 py-3 text-left">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($topSold as $row)
                                <tr>
                                    <td class="px-4 py-3">{{ optional($row['product'])->name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ optional($row['product'])->sku ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ optional($row['product'])->barcode ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ number_format((float) $row['sold_qty'], 2) }}</td>
                                    <td class="px-4 py-3">
                                        @if ($row['product'])
                                            <a href="{{ route('products.show', $row['product']) }}" class="text-slate-700 hover:text-slate-900 font-medium">{{ __('Details') }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
