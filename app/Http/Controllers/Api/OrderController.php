<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\BuildsMonthlyMetrics;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use BuildsMonthlyMetrics;

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $query = Order::query()->with(['client', 'user']);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('number', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('company_name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function summary()
    {
        $chart = $this->monthlyCountSeries(Order::class);
        $fromDate = now()->subDays(30)->startOfDay();

        $salesByHourRaw = Order::query()
            ->selectRaw('HOUR(COALESCE(ordered_at, created_at)) as hour, COUNT(*) as total')
            ->where('status', 'completed')
            ->whereRaw('COALESCE(ordered_at, created_at) >= ?', [$fromDate])
            ->groupBy(DB::raw('HOUR(COALESCE(ordered_at, created_at))'))
            ->pluck('total', 'hour');

        $salesByHour = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $salesByHour[] = [
                'hour' => $hour,
                'total' => (int) ($salesByHourRaw[$hour] ?? 0),
            ];
        }

        return response()->json([
            'metrics' => [
                ['label' => 'Total orders', 'value' => (int) Order::query()->count()],
                ['label' => 'Pending orders', 'value' => (int) Order::query()->where('status', 'pending')->count()],
                ['label' => 'Shipped orders', 'value' => (int) Order::query()->whereNotNull('shipped_at')->count()],
                ['label' => 'Order value', 'value' => (float) Order::query()->sum('total')],
                ['label' => 'Growth vs last month', 'value' => $this->monthlyTrend($chart['values'])],
            ],
            'chart' => [
                'label' => 'Orders created (last 6 months)',
                'labels' => $chart['labels'],
                'values' => $chart['values'],
            ],
            'sales_by_hour' => $salesByHour,
        ]);
    }
}
