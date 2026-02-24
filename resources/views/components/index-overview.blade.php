@props([
    'description' => null,
    'metrics' => [],
    'chart' => ['label' => 'Activity', 'labels' => [], 'values' => []],
])

@php
    $values = collect($chart['values'] ?? []);
    $labels = $chart['labels'] ?? [];
    $maxValue = max(1, (int) $values->max());
@endphp

<div class="space-y-6">
    @if ($description)
        <div class="rounded-xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-sm text-slate-600">{{ $description }}</p>
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($metrics as $metric)
            <div class="rounded-xl bg-white border border-slate-200 p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    {{ $metric['label'] ?? '' }}
                </p>
                <p class="mt-2 text-2xl font-bold text-slate-900">
                    {{ $metric['value'] ?? '0' }}
                </p>
            </div>
        @endforeach
    </div>

    <div class="rounded-xl bg-white border border-slate-200 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-900">{{ $chart['label'] ?? 'Activity' }}</h3>

        <div class="mt-6">
            @if ($values->isEmpty())
                <p class="text-sm text-slate-500">No data available yet.</p>
            @else
                <div class="h-56 flex items-end gap-3">
                    @foreach ($values as $index => $value)
                        @php
                            $height = max(8, (int) round(($value / $maxValue) * 100));
                        @endphp

                        <div class="flex-1 flex flex-col items-center justify-end gap-2 min-w-0">
                            <span class="text-xs text-slate-500">{{ $value }}</span>
                            <div class="w-full rounded-t-md bg-gradient-to-t from-emerald-600 to-emerald-400" style="height: {{ $height }}%;"></div>
                            <span class="text-xs font-medium text-slate-400">{{ $labels[$index] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
