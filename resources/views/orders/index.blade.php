<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Orders') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <x-index-overview :description="$pageDescription" :metrics="$metrics" :chart="$chart" :list-route="route('orders.list')" />

        <section class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Sales heatmap by hour</h3>
                    <p class="text-sm text-slate-500">Completed orders over the last 30 days.</p>
                </div>
            </div>

            @php
                $maxHour = max(1, (int) collect($salesByHour)->max('total'));
            @endphp

            <div class="mt-4 space-y-2">
                @foreach ($salesByHour as $row)
                    @php
                        $hourLabel = str_pad((string) $row['hour'], 2, '0', STR_PAD_LEFT).'h';
                        $percent = ($row['total'] / $maxHour) * 100;
                    @endphp
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-10 text-slate-600">{{ $hourLabel }}</div>
                        <div class="flex-1">
                            <div class="h-3 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-3 bg-emerald-500" style="width: {{ $percent }}%;"></div>
                            </div>
                        </div>
                        <div class="w-12 text-right font-semibold {{ $row['total'] > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $row['total'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
