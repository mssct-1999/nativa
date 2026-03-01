<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMonthlyMetrics;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;

class DashboardController extends Controller
{
    use BuildsMonthlyMetrics;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $chart = $this->monthlyCountSeries(Order::class);
        $latestPayments = Payment::query()
            ->with(['invoice'])
            ->orderByDesc('paid_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'pageDescription' => 'Business activity at a glance across your main operations.',
            'metrics' => [
                ['label' => 'Clients', 'value' => number_format(Client::query()->count())],
                ['label' => 'Products', 'value' => number_format(Product::query()->count())],
                ['label' => 'Open Orders', 'value' => number_format(Order::query()->where('status', '!=', 'delivered')->count())],
                ['label' => 'Team Members', 'value' => number_format(Employee::query()->count())],
                ['label' => 'Invoices (30d)', 'value' => number_format(Invoice::query()->where('created_at', '>=', now()->subDays(30))->count())],
                ['label' => 'Orders Trend', 'value' => $this->monthlyTrend($chart['values'])],
            ],
            'chart' => [
                'label' => 'Orders created (last 6 months)',
                'labels' => $chart['labels'],
                'values' => $chart['values'],
            ],
            'latestPayments' => $latestPayments,
        ]);
    }
}
