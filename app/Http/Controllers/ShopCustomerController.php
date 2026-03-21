<?php

namespace App\Http\Controllers;

use App\Models\ShopCustomer;
use Illuminate\Http\Request;

class ShopCustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $query = ShopCustomer::query()->with('user');

        if ($search !== '') {
            $query->whereHas('user', function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        return view('shop_customers.index', [
            'customers' => $query->orderByDesc('total_spent')->paginate(15)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function show(ShopCustomer $shopCustomer)
    {
        $shopCustomer->load([
            'user',
            'orders' => function ($query) {
                $query->orderByDesc('created_at');
            },
        ]);

        return view('shop_customers.show', [
            'customer' => $shopCustomer,
        ]);
    }

    public function edit(ShopCustomer $shopCustomer)
    {
        return view('shop_customers.edit', [
            'customer' => $shopCustomer->load('user'),
        ]);
    }

    public function update(Request $request, ShopCustomer $shopCustomer)
    {
        $validated = $request->validate([
            'fidelity_score' => ['required', 'integer', 'min:0'],
            'discount_rate' => ['required', 'numeric', 'min:0', 'max:50'],
        ]);

        $shopCustomer->update($validated);

        return redirect()->route('shop-customers.show', $shopCustomer)->with('status', 'Customer updated successfully.');
    }
}
