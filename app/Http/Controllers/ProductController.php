<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMonthlyMetrics;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    use BuildsMonthlyMetrics;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chart = $this->monthlyCountSeries(Product::class);
        $fromDate = now()->subDays(30)->startOfDay();

        $topSold = InventoryMovement::query()
            ->selectRaw('product_id, SUM(quantity) as sold_qty')
            ->where('type', 'out')
            ->where('reference_type', 'orders')
            ->where('created_at', '>=', $fromDate)
            ->groupBy('product_id')
            ->orderByDesc('sold_qty')
            ->with('product')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                return [
                    'product' => $row->product,
                    'sold_qty' => (float) $row->sold_qty,
                ];
            });

        return view('products.index', [
            'pageDescription' => 'Monitor catalog size, pricing profile, and recent product additions.',
            'metrics' => [
                ['label' => 'Total products', 'value' => number_format(Product::query()->count())],
                ['label' => 'Active products', 'value' => number_format(Product::query()->where('active', true)->count())],
                ['label' => 'Avg. price', 'value' => '$'.number_format((float) Product::query()->avg('price'), 2)],
                ['label' => 'Growth vs last month', 'value' => $this->monthlyTrend($chart['values'])],
            ],
            'chart' => [
                'label' => 'Products created (last 6 months)',
                'labels' => $chart['labels'],
                'values' => $chart['values'],
            ],
            'topSold' => $topSold,
            'topSoldRangeDays' => 30,
        ]);
    }

    /**
     * Display the full list view used for CRUD operations.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        // Test barcode scanner - return random product
        if ($request->query('get_random') === '1') {
            $product = Product::query()
                ->where('active', true)
                ->inRandomOrder()
                ->first();
            
            if (!$product) {
                return response()->json(['barcode' => null, 'message' => 'No active products found'], 404);
            }
            
            return response()->json([
                'barcode' => $product->barcode,
                'name' => $product->name,
                'sku' => $product->sku
            ]);
        }

        $search = trim((string) $request->query('q', ''));

        $query = Product::query();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%')
                    ->orWhere('barcode', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        return view('products.list', [
            'products' => $query->latest()->paginate(15)->withQueryString(),
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('products.create');
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
            'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:255', 'unique:products,barcode'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['taxable'] = $request->boolean('taxable');
        $validated['active'] = $request->boolean('active');

        Product::create($validated);

        return redirect()->route('products.list')->with('status', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $rangeDays = 30;
        $fromDate = now()->subDays($rangeDays - 1)->startOfDay();

        $movements = \App\Models\InventoryMovement::query()
            ->where('product_id', $product->id)
            ->where('created_at', '>=', $fromDate)
            ->orderBy('created_at')
            ->get();

        $daily = [];
        for ($i = 0; $i < $rangeDays; $i++) {
            $day = now()->subDays($rangeDays - 1 - $i)->format('Y-m-d');
            $daily[$day] = ['date' => $day, 'in' => 0.0, 'out' => 0.0];
        }

        foreach ($movements as $movement) {
            $day = $movement->created_at->format('Y-m-d');
            if (! isset($daily[$day])) {
                continue;
            }
            if ($movement->type === 'in') {
                $daily[$day]['in'] += (float) $movement->quantity;
            } elseif ($movement->type === 'out') {
                $daily[$day]['out'] += (float) $movement->quantity;
            }
        }

        $salesMovements = $movements->filter(function ($movement) {
            return $movement->type === 'out' && $movement->reference_type === 'orders';
        });

        $sales30 = $salesMovements->sum('quantity');
        $sales14 = $salesMovements->filter(function ($movement) {
            return $movement->created_at >= now()->subDays(14);
        })->sum('quantity');
        $dailyRate = $sales14 > 0 ? ($sales14 / 14) : 0.0;
        $forecast7 = $dailyRate * 7;

        $totalsByProduct = \App\Models\InventoryMovement::query()
            ->selectRaw('product_id, SUM(quantity) as total')
            ->where('type', 'out')
            ->where('reference_type', 'orders')
            ->where('created_at', '>=', $fromDate)
            ->groupBy('product_id')
            ->pluck('total');

        $avgSales = (float) $totalsByProduct->avg();
        $popularity = $avgSales > 0 ? min(100, (int) round(($sales30 / $avgSales) * 100)) : 0;

        $inventories = \App\Models\Inventory::query()
            ->where('product_id', $product->id)
            ->with('warehouse')
            ->get();

        $totalQty = (float) $inventories->sum('quantity');
        $totalReserved = (float) $inventories->sum('reserved');
        $totalReorder = (float) $inventories->sum('reorder_point');
        $availableQty = $totalQty - $totalReserved;

        $reorderNeededNow = $availableQty <= $totalReorder;
        $reorderDate = null;
        $daysToReorder = null;

        if (! $reorderNeededNow && $dailyRate > 0) {
            $daysToReorder = (int) floor(($availableQty - $totalReorder) / $dailyRate);
            $reorderDate = now()->addDays(max(0, $daysToReorder));
        }

        return view('products.show', [
            'product' => $product,
            'dailyMovements' => array_values($daily),
            'sales30' => (float) $sales30,
            'dailyRate' => $dailyRate,
            'forecast7' => $forecast7,
            'popularity' => $popularity,
            'inventories' => $inventories,
            'availableQty' => $availableQty,
            'totalReorder' => $totalReorder,
            'reorderNeededNow' => $reorderNeededNow,
            'reorderDate' => $reorderDate,
            'daysToReorder' => $daysToReorder,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($product->id)],
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products', 'barcode')->ignore($product->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['taxable'] = $request->boolean('taxable');
        $validated['active'] = $request->boolean('active');

        $product->update($validated);

        return redirect()->route('products.list')->with('status', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.list')->with('status', 'Product deleted successfully.');
    }
}
