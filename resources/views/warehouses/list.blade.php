<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Warehouses List') }}
            </h2>
            <a href="{{ route('warehouses.create') }}" class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                Add new warehouse
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4">
        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Warehouse</th>
                        <th class="px-4 py-3 text-left">Location</th>
                        <th class="px-4 py-3 text-left">Products / Quantity Graph</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($warehouses as $warehouse)
                        @php
                            $chart = $warehouseCharts[$warehouse->id] ?? ['total_quantity' => 0, 'top_products' => collect()];
                            $topProducts = collect($chart['top_products'] ?? []);
                        @endphp
                        <tr>
                            <td class="px-4 py-4 align-top">
                                <p class="font-semibold text-slate-900">{{ $warehouse->name }}</p>
                                <p class="text-xs text-slate-500 mt-1">
                                    Code: {{ $warehouse->code ?: '-' }}
                                </p>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <p>{{ $warehouse->location ?: '-' }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $warehouse->contact ?: '-' }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-xs uppercase tracking-wide text-slate-500">
                                    Total qty: {{ number_format((float) ($chart['total_quantity'] ?? 0), 2) }}
                                </p>

                                @if ($topProducts->isEmpty())
                                    <p class="mt-2 text-sm text-slate-500">No inventory registered.</p>
                                @else
                                    <div class="mt-3 space-y-2">
                                        @foreach ($topProducts as $product)
                                            <div>
                                                <div class="flex items-center justify-between gap-3">
                                                    <span class="text-xs text-slate-600 truncate">{{ $product['name'] }}</span>
                                                    <span class="text-xs font-medium text-slate-800">{{ number_format((float) $product['quantity'], 2) }}</span>
                                                </div>
                                                <div class="mt-1 h-2 rounded-full bg-slate-100">
                                                    <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $product['percent'] }}%;"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 align-top">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('warehouses.edit', $warehouse) }}" class="text-amber-600 hover:text-amber-700 font-medium">Edit</a>
                                    <form method="POST" action="{{ route('warehouses.destroy', $warehouse) }}" onsubmit="return confirm('Delete this warehouse?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-500">No warehouses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $warehouses->links() }}
        </div>
    </div>
</x-app-layout>
