@props([
    'description' => null,
    'metrics' => [],
    'chart' => ['label' => 'Activity', 'labels' => [], 'values' => []],
    'listRoute' => null,
])

@php
    $values = collect($chart['values'] ?? []);
    $labels = $chart['labels'] ?? [];
    $maxValue = max(1, (int) $values->max());
@endphp

<div class="space-y-6">
    @if ($description)
        <div class="rounded-xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-600">{{ $description }}</p>
                @if ($listRoute)
                    <a
                        href="{{ $listRoute }}"
                        class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700"
                    >
                        View full list
                    </a>
                @endif
            </div>
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

                        <div class="flex-1 flex flex-col items-center gap-2 min-w-0 h-full">
                            <span class="text-xs text-slate-500">{{ $value }}</span>
                            <div class="w-full flex-1 flex items-end">
                                <div class="w-full rounded-t-md bg-gradient-to-t from-emerald-600 to-emerald-400" style="height: {{ $height }}%;"></div>
                            </div>
                            <span class="text-xs font-medium text-slate-400">{{ $labels[$index] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
