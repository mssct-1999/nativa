<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
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

        return response()->json($query->latest()->paginate(15));
    }

    public function show(Product $product)
    {
        $rangeDays = 30;
        $fromDate = now()->subDays($rangeDays - 1)->startOfDay();

        $movements = InventoryMovement::query()
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

        $totalsByProduct = InventoryMovement::query()
            ->selectRaw('product_id, SUM(quantity) as total')
            ->where('type', 'out')
            ->where('reference_type', 'orders')
            ->where('created_at', '>=', $fromDate)
            ->groupBy('product_id')
            ->pluck('total');

        $avgSales = (float) $totalsByProduct->avg();
        $popularity = $avgSales > 0 ? min(100, (int) round(($sales30 / $avgSales) * 100)) : 0;

        $inventories = Inventory::query()
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
            $reorderDate = now()->addDays(max(0, $daysToReorder))->format('Y-m-d');
        }

        return response()->json([
            'product' => $product,
            'daily_movements' => array_values($daily),
            'sales30' => (float) $sales30,
            'daily_rate' => $dailyRate,
            'forecast7' => $forecast7,
            'popularity' => $popularity,
            'inventories' => $inventories,
            'available_qty' => $availableQty,
            'total_reorder' => $totalReorder,
            'reorder_needed_now' => $reorderNeededNow,
            'reorder_date' => $reorderDate,
            'days_to_reorder' => $daysToReorder,
        ]);
    }
}
