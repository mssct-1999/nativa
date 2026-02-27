<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMonthlyMetrics;
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
        ]);
    }

    /**
     * Display the full list view used for CRUD operations.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return view('products.list', [
            'products' => Product::query()->latest()->paginate(15),
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
        //
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
