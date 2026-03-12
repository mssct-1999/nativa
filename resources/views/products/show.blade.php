<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                    {{ $product->name }}
                </h2>
                <p class="text-sm text-slate-500">
                    SKU: {{ $product->sku ?: '-' }} | Barcode: {{ $product->barcode ?: '-' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Edit product
                </a>
                <a href="{{ route('products.list') }}" class="text-sm text-slate-600 hover:text-slate-900">Back to list</a>
            </div>
        </div>
    </x-slot>

    @php
        $maxValue = max(1, (float) collect($dailyMovements)->flatMap(function ($row) {
            return [(float) $row['in'], (float) $row['out']];
        })->max());
    @endphp

    <div class="max-w-7xl mx-auto space-y-6">
        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Stock movement (last 30 days)</h3>
                    <p class="text-sm text-slate-500">Green bars show reorders (in). Red bars show sales (out).</p>
                </div>
                <div class="text-sm text-slate-600">
                    Total sold: <span class="font-semibold text-slate-900">{{ number_format((float) $sales30, 2) }}</span>
                </div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <div class="min-w-[900px]">
                    <div class="flex items-center gap-2 h-56 rounded-lg border border-slate-100 bg-slate-50 px-3 py-4">
                        @foreach ($dailyMovements as $index => $row)
                            @php
                                $inHeight = $maxValue > 0 ? (($row['in'] / $maxValue) * 100) : 0;
                                $outHeight = $maxValue > 0 ? (($row['out'] / $maxValue) * 100) : 0;
                                $showLabel = ($index % 5) === 0;
                            @endphp
                            <div class="flex flex-col items-center w-6">
                                <div class="h-20 w-full flex items-end justify-center">
                                    <div class="w-3 rounded-t-md bg-emerald-500" style="height: {{ $inHeight }}%;" title="In: {{ number_format((float) $row['in'], 2) }}"></div>
                                </div>
                                <div class="h-px w-full bg-slate-200"></div>
                                <div class="h-20 w-full flex items-start justify-center">
                                    <div class="w-3 rounded-b-md bg-rose-500" style="height: {{ $outHeight }}%;" title="Out: {{ number_format((float) $row['out'], 2) }}"></div>
                                </div>
                                <div class="mt-1 text-[10px] text-slate-500">{{ $showLabel ? $row['date'] : '' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-3">
            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-3">
                <h3 class="text-base font-semibold text-slate-900">Popularity</h3>
                <p class="text-sm text-slate-600">Score based on sales vs average product sales in last 30 days.</p>
                <div class="w-full rounded-full bg-slate-100 h-3 overflow-hidden">
                    <div class="h-3 bg-emerald-500" style="width: {{ $popularity }}%;"></div>
                </div>
                <p class="text-sm text-slate-700">Popularity score: <span class="font-semibold">{{ $popularity }} / 100</span></p>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-3">
                <h3 class="text-base font-semibold text-slate-900">Sales forecast</h3>
                <p class="text-sm text-slate-600">Forecast for the next 7 days using recent average daily sales.</p>
                <p class="text-2xl font-semibold text-slate-900">{{ number_format((float) $forecast7, 2) }}</p>
                <p class="text-sm text-slate-500">Avg daily sales: {{ number_format((float) $dailyRate, 2) }}</p>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-3">
                <h3 class="text-base font-semibold text-slate-900">Reorder estimate</h3>
                <p class="text-sm text-slate-600">Based on available stock vs reorder point and recent sales.</p>
                <p class="text-sm text-slate-700">
                    Available stock: <span class="font-semibold">{{ number_format((float) $availableQty, 2) }}</span>
                </p>
                <p class="text-sm text-slate-700">
                    Reorder point: <span class="font-semibold">{{ number_format((float) $totalReorder, 2) }}</span>
                </p>
                @if ($reorderNeededNow)
                    <p class="text-sm font-semibold text-rose-600">Reorder needed now.</p>
                @elseif ($reorderDate)
                    <p class="text-sm text-slate-700">
                        Estimated reorder date:
                        <span class="font-semibold">{{ $reorderDate->format('Y-m-d') }}</span>
                        (in {{ $daysToReorder }} days)
                    </p>
                @else
                    <p class="text-sm text-slate-500">Not enough sales data to estimate reorder date.</p>
                @endif
            </section>
        </div>

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-semibold text-slate-900">Warehouse stock</h3>
            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left">Warehouse</th>
                            <th class="px-4 py-3 text-left">Quantity</th>
                            <th class="px-4 py-3 text-left">Reserved</th>
                            <th class="px-4 py-3 text-left">Reorder point</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($inventories as $inventory)
                            <tr>
                                <td class="px-4 py-3">{{ optional($inventory->warehouse)->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ number_format((float) $inventory->quantity, 2) }}</td>
                                <td class="px-4 py-3">{{ number_format((float) $inventory->reserved, 2) }}</td>
                                <td class="px-4 py-3">{{ number_format((float) $inventory->reorder_point, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-500">No inventory records for this product.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
