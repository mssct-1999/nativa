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
                @php
                    $maxSold = max(1, (float) $topSold->max('sold_qty'));
                @endphp
                <div class="mt-4 space-y-3">
                    @foreach ($topSold as $row)
                        @php
                            $product = $row['product'] ?? null;
                            $soldQty = (float) $row['sold_qty'];
                            $percent = ($soldQty / $maxSold) * 100;
                        @endphp
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="w-40 text-sm font-medium text-slate-900 truncate">
                                {{ $product ? $product->name : '-' }}
                            </div>
                            <div class="flex-1 min-w-[200px]">
                                <div class="h-3 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-3 bg-emerald-500" style="width: {{ $percent }}%;"></div>
                                </div>
                            </div>
                            <div class="w-24 text-right text-sm font-semibold {{ $soldQty > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ number_format($soldQty, 2) }}
                            </div>
                            <div class="w-20 text-right text-sm">
                                @if ($product)
                                    <a href="{{ route('products.show', $product) }}" class="text-slate-700 hover:text-slate-900 font-medium">{{ __('Details') }}</a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
