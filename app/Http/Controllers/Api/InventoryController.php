<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $query = Inventory::query()->with(['product', 'warehouse']);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->whereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('sku', 'like', '%'.$search.'%')
                            ->orWhere('barcode', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('warehouse', function ($warehouseQuery) use ($search) {
                        $warehouseQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('code', 'like', '%'.$search.'%')
                            ->orWhere('location', 'like', '%'.$search.'%');
                    });
            });
        }

        return response()->json($query->latest()->paginate(15));
    }
}
