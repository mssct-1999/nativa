<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Warehouses List') }}
            </h2>
            <a href="{{ route('warehouses.create') }}" class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                {{ __('Add new warehouse') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4">
        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="GET" action="{{ route('warehouses.list') }}" class="flex flex-wrap items-center gap-3">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="{{ __('Search warehouse, code, location...') }}"
                class="w-full max-w-md rounded-md border-slate-300 text-sm"
            />
            <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                {{ __('Search') }}
            </button>
            @if (!empty($search))
                <a href="{{ route('warehouses.list') }}" class="text-sm text-slate-600 hover:text-slate-900">{{ __('Clear') }}</a>
            @endif
        </form>

        @forelse ($warehouses as $warehouse)
            @php
                $chart = $warehouseCharts[$warehouse->id] ?? [
                    'total_quantity' => 0,
                    'product_count' => 0,
                    'max_quantity' => 0,
                    'products' => collect(),
                ];
                $products = collect($chart['products'] ?? []);
            @endphp

            <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ $warehouse->name }}</h3>
                        <p class="mt-1 text-sm text-slate-600">
                            {{ $warehouse->code ?: 'No code' }} | {{ $warehouse->location ?: 'No location' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3 text-sm">
                        <a href="{{ route('warehouses.edit', $warehouse) }}" class="text-amber-600 hover:text-amber-700 font-medium">{{ __('Edit') }}</a>
                        <form method="POST" action="{{ route('warehouses.destroy', $warehouse) }}" onsubmit="return confirm('{{ __('Delete this warehouse?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 font-medium">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-xs text-slate-600">
                    <p>{{ __('Total quantity') }}: <span class="font-semibold text-slate-900">{{ number_format((float) ($chart['total_quantity'] ?? 0), 2) }}</span></p>
                    <p>{{ __('Products') }}: <span class="font-semibold text-slate-900">{{ number_format((int) ($chart['product_count'] ?? 0)) }}</span></p>
                    <p>{{ __('Highest qty item') }}: <span class="font-semibold text-slate-900">{{ number_format((float) ($chart['max_quantity'] ?? 0), 2) }}</span></p>
                </div>

                @if ($products->isEmpty())
                    <p class="mt-4 text-sm text-slate-500">{{ __('No inventory registered for this warehouse.') }}</p>
                @else
                    <div class="mt-4 overflow-x-auto pb-2">
                        <div class="h-64 min-w-full border border-slate-100 rounded-lg bg-slate-50 p-3">
                            <div class="h-full flex items-end gap-2">
                                @foreach ($products as $product)
                                    @php
                                        $barHeight = max(0, min(100, (float) $product['percent']));
                                    @endphp
                                    <div class="w-12 flex-none">
                                        <div class="h-44 flex items-end justify-center">
                                            <div
                                                class="w-8 rounded-t-md bg-emerald-500"
                                                style="height: {{ $barHeight }}%;"
                                                title="{{ $product['name'] }}: {{ number_format((float) $product['quantity'], 2) }}"
                                            ></div>
                                        </div>
                                        <p class="mt-1 text-center text-[10px] font-medium text-slate-800">{{ number_format((float) $product['quantity'], 0) }}</p>
                                        <p class="text-center text-[10px] text-slate-500 truncate" title="{{ $product['name'] }}">
                                            {{ \Illuminate\Support\Str::limit($product['name'], 10) }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </section>
        @empty
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-6 text-center text-slate-500 shadow-sm">
                {{ __('No warehouses found.') }}
            </div>
        @endforelse

        <div>
            {{ $warehouses->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
