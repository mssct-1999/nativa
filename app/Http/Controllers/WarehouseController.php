<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMonthlyMetrics;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    use BuildsMonthlyMetrics;

    /**
     * Display warehouse overview.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chart = $this->monthlyCountSeries(Warehouse::class);

        return view('warehouses.index', [
            'pageDescription' => 'Monitor warehouse footprint and product stock distribution.',
            'metrics' => [
                ['label' => 'Total warehouses', 'value' => number_format(Warehouse::query()->count())],
                ['label' => 'Warehouses with stock', 'value' => number_format(Warehouse::query()->whereHas('inventories')->count())],
                ['label' => 'Total inventory quantity', 'value' => number_format((float) \App\Models\Inventory::query()->sum('quantity'), 0)],
                ['label' => 'Growth vs last month', 'value' => $this->monthlyTrend($chart['values'])],
            ],
            'chart' => [
                'label' => 'Warehouses created (last 6 months)',
                'labels' => $chart['labels'],
                'values' => $chart['values'],
            ],
        ]);
    }

    /**
     * Display warehouses with stock chart per location.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $query = Warehouse::query();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('code', 'like', '%'.$search.'%')
                    ->orWhere('location', 'like', '%'.$search.'%')
                    ->orWhere('contact', 'like', '%'.$search.'%');
            });
        }

        $warehouses = $query
            ->with(['inventories.product'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $warehouseCharts = [];

        foreach ($warehouses as $warehouse) {
            $inventories = $warehouse->inventories
                ->sortByDesc('quantity')
                ->values();

            $maxQuantity = max(1, (float) $inventories->max('quantity'));

            $warehouseCharts[$warehouse->id] = [
                'total_quantity' => (float) $inventories->sum('quantity'),
                'product_count' => $inventories->count(),
                'max_quantity' => $maxQuantity,
                'products' => $inventories->map(function ($inventory) use ($maxQuantity) {
                    $quantity = (float) $inventory->quantity;

                    return [
                        'name' => optional($inventory->product)->name ?? ('Product #'.$inventory->product_id),
                        'quantity' => $quantity,
                        'percent' => $maxQuantity > 0 ? ($quantity / $maxQuantity) * 100 : 0,
                    ];
                })->values(),
            ];
        }

        return view('warehouses.list', [
            'warehouses' => $warehouses,
            'warehouseCharts' => $warehouseCharts,
            'search' => $search,
        ]);
    }

    /**
     * Show form to create a warehouse.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('warehouses.create');
    }

    /**
     * Store a newly created warehouse.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255', 'unique:warehouses,code'],
            'location' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        Warehouse::create($validated);

        return redirect()->route('warehouses.list')->with('status', 'Warehouse created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse)
    {
        return redirect()->route('warehouses.edit', $warehouse);
    }

    /**
     * Show the form for editing the specified warehouse.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', [
            'warehouse' => $warehouse,
        ]);
    }

    /**
     * Update the specified warehouse.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255', Rule::unique('warehouses', 'code')->ignore($warehouse->id)],
            'location' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $warehouse->update($validated);

        return redirect()->route('warehouses.list')->with('status', 'Warehouse updated successfully.');
    }

    /**
     * Remove warehouse.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()->route('warehouses.list')->with('status', 'Warehouse deleted successfully.');
    }
}
