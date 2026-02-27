<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMonthlyMetrics;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    use BuildsMonthlyMetrics;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chart = $this->monthlyCountSeries(Inventory::class);

        return view('inventory.index', [
            'pageDescription' => 'Watch stock levels, reorder risk, and inventory additions.',
            'metrics' => [
                ['label' => 'Inventory records', 'value' => number_format(Inventory::query()->count())],
                ['label' => 'Total quantity', 'value' => number_format((float) Inventory::query()->sum('quantity'), 0)],
                ['label' => 'Reserved quantity', 'value' => number_format((float) Inventory::query()->sum('reserved'), 0)],
                ['label' => 'Below reorder point', 'value' => number_format(Inventory::query()->whereColumn('quantity', '<=', 'reorder_point')->count())],
                ['label' => 'Growth vs last month', 'value' => $this->monthlyTrend($chart['values'])],
            ],
            'chart' => [
                'label' => 'Inventory records created (last 6 months)',
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
        return view('inventory.list', [
            'inventories' => Inventory::query()->with(['product', 'warehouse'])->latest()->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inventory.create', [
            'products' => Product::query()->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->orderBy('name')->get(),
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
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => [
                'required',
                'exists:warehouses,id',
                Rule::unique('inventories')->where(function ($query) use ($request) {
                    return $query->where('product_id', $request->input('product_id'));
                }),
            ],
            'quantity' => ['required', 'numeric', 'min:0'],
            'reserved' => ['required', 'numeric', 'min:0'],
            'reorder_point' => ['required', 'numeric', 'min:0'],
        ], [
            'warehouse_id.unique' => 'This product is already registered for the selected warehouse.',
        ]);

        Inventory::create($validated);

        return redirect()->route('inventory.list')->with('status', 'Inventory record created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Inventory $inventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventory $inventory)
    {
        return view('inventory.edit', [
            'inventory' => $inventory,
            'products' => Product::query()->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => [
                'required',
                'exists:warehouses,id',
                Rule::unique('inventories')->where(function ($query) use ($request) {
                    return $query->where('product_id', $request->input('product_id'));
                })->ignore($inventory->id),
            ],
            'quantity' => ['required', 'numeric', 'min:0'],
            'reserved' => ['required', 'numeric', 'min:0'],
            'reorder_point' => ['required', 'numeric', 'min:0'],
        ], [
            'warehouse_id.unique' => 'This product is already registered for the selected warehouse.',
        ]);

        $inventory->update($validated);

        return redirect()->route('inventory.list')->with('status', 'Inventory record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('inventory.list')->with('status', 'Inventory record deleted successfully.');
    }
}
