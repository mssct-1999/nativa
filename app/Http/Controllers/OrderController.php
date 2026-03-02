<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMonthlyMetrics;
use App\Models\Client;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    use BuildsMonthlyMetrics;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chart = $this->monthlyCountSeries(Order::class);

        return view('orders.index', [
            'pageDescription' => 'See order velocity, pipeline status, and revenue volume.',
            'metrics' => [
                ['label' => 'Total orders', 'value' => number_format(Order::query()->count())],
                ['label' => 'Pending orders', 'value' => number_format(Order::query()->where('status', 'pending')->count())],
                ['label' => 'Shipped orders', 'value' => number_format(Order::query()->whereNotNull('shipped_at')->count())],
                ['label' => 'Order value', 'value' => '$'.number_format((float) Order::query()->sum('total'), 2)],
                ['label' => 'Growth vs last month', 'value' => $this->monthlyTrend($chart['values'])],
            ],
            'chart' => [
                'label' => 'Orders created (last 6 months)',
                'labels' => $chart['labels'],
                'values' => $chart['values'],
            ],
        ]);
    }

    /**
     * Display the full list view used for CRUD operations.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $timelineOrders = Order::query()
            ->with(['client', 'user'])
            ->orderByRaw('COALESCE(ordered_at, created_at) DESC')
            ->orderByDesc('id')
            ->limit(150)
            ->get();

        $timeline = $timelineOrders
            ->groupBy(function (Order $order) {
                return optional($order->ordered_at ?? $order->created_at)->format('Y-m-d');
            })
            ->map(function ($orders, $day) {
                return [
                    'day' => $day,
                    'day_total' => (float) $orders->sum('total'),
                    'orders' => $orders,
                ];
            })
            ->values();

        return view('orders.list', [
            'orders' => Order::query()->with(['client', 'user'])->latest()->paginate(15),
            'timeline' => $timeline,
        ]);
    }

    /**
     * Quickly mark an order as shipped from the timeline view.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function markShipped(Order $order)
    {
        if ($order->shipped_at !== null) {
            return redirect()->route('orders.list')->with('status', 'Order is already marked as shipped.');
        }

        $order->shipped_at = now();

        if (in_array($order->status, ['pending', 'processing'], true)) {
            $order->status = 'completed';
        }

        $order->save();

        return redirect()->route('orders.list')->with('status', 'Order marked as shipped.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('orders.create', [
            'clients' => Client::query()->orderBy('company_name')->get(),
            'users' => User::query()->orderBy('name')->get(),
            'statuses' => ['pending', 'processing', 'completed', 'cancelled'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => ['required', 'string', 'max:255', 'unique:orders,number'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'created_by' => ['nullable', 'exists:users,id'],
            'status' => ['required', Rule::in(['pending', 'processing', 'completed', 'cancelled'])],
            'total' => ['required', 'numeric', 'min:0'],
            'ordered_at' => ['nullable', 'date'],
            'shipped_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        Order::create($validated);

        return redirect()->route('orders.list')->with('status', 'Order created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        return view('orders.edit', [
            'order' => $order,
            'clients' => Client::query()->orderBy('company_name')->get(),
            'users' => User::query()->orderBy('name')->get(),
            'statuses' => ['pending', 'processing', 'completed', 'cancelled'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'number' => ['required', 'string', 'max:255', Rule::unique('orders', 'number')->ignore($order->id)],
            'client_id' => ['nullable', 'exists:clients,id'],
            'created_by' => ['nullable', 'exists:users,id'],
            'status' => ['required', Rule::in(['pending', 'processing', 'completed', 'cancelled'])],
            'total' => ['required', 'numeric', 'min:0'],
            'ordered_at' => ['nullable', 'date'],
            'shipped_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $order->update($validated);

        return redirect()->route('orders.list')->with('status', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('orders.list')->with('status', 'Order deleted successfully.');
    }
}
