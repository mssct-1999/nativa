<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $query = Transaction::query()->with('account');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('description', 'like', '%'.$search.'%')
                    ->orWhere('reference_type', 'like', '%'.$search.'%')
                    ->orWhere('reference_id', 'like', '%'.$search.'%')
                    ->orWhereHas('account', function ($accountQuery) use ($search) {
                        $accountQuery->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        return response()->json($query->latest()->paginate(20));
    }
}
