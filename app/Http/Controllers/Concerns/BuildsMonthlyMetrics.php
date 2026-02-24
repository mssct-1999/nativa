<?php

namespace App\Http\Controllers\Concerns;

trait BuildsMonthlyMetrics
{
    protected function monthlyCountSeries(string $modelClass, int $months = 6): array
    {
        $labels = [];
        $values = [];
        $firstMonth = now()->startOfMonth()->subMonths($months - 1);

        for ($i = 0; $i < $months; $i++) {
            $monthStart = $firstMonth->copy()->addMonths($i);
            $monthEnd = $monthStart->copy()->endOfMonth();

            $labels[] = $monthStart->format('M');
            $values[] = $modelClass::query()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    protected function monthlyTrend(array $values): string
    {
        if (count($values) < 2) {
            return '0%';
        }

        $current = (int) $values[count($values) - 1];
        $previous = (int) $values[count($values) - 2];

        if ($previous === 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $change = (($current - $previous) / $previous) * 100;
        $prefix = $change > 0 ? '+' : '';

        return $prefix.number_format($change, 1).'%';
    }
}
