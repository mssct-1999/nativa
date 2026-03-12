<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function lookup(Request $request)
    {
        $validated = $request->validate([
            'barcode' => ['required', 'string', 'max:255'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
        ]);

        $barcode = trim($validated['barcode']);

        $query = Product::query()
            ->where('barcode', $barcode)
            ->orWhere('sku', $barcode);

        if (ctype_digit($barcode)) {
            $query->orWhere('id', (int) $barcode);
        }

        $product = $query->first();

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $inventoryQuery = Inventory::query()->where('product_id', $product->id);

        if (! empty($validated['warehouse_id'])) {
            $inventoryQuery->where('warehouse_id', $validated['warehouse_id']);
        }

        $inventory = $inventoryQuery->first();
        $available = $inventory ? (float) $inventory->quantity - (float) $inventory->reserved : 0;

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'barcode' => $product->barcode,
            'price' => (float) $product->price,
            'description' => $product->description,
            'available' => $available,
        ]);
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'max:255'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
        ]);

        $query = trim($validated['q']);

        $products = Product::query()
            ->where('name', 'like', '%'.$query.'%')
            ->orderBy('name')
            ->limit(8)
            ->get();

        $warehouseId = $validated['warehouse_id'] ?? null;

        $results = $products->map(function (Product $product) use ($warehouseId) {
            $inventoryQuery = Inventory::query()->where('product_id', $product->id);
            if ($warehouseId) {
                $inventoryQuery->where('warehouse_id', $warehouseId);
            }
            $inventory = $inventoryQuery->first();
            $available = $inventory ? (float) $inventory->quantity - (float) $inventory->reserved : 0;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'price' => (float) $product->price,
                'description' => $product->description,
                'available' => $available,
            ];
        });

        return response()->json([
            'results' => $results,
        ]);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['nullable', 'exists:clients,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'channel' => ['required', Rule::in(['online', 'physical'])],
            'issue_invoice' => ['required', 'boolean'],
            'paid' => ['required', 'boolean'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
        ]);

        if ($validated['paid'] && empty($validated['account_id'])) {
            throw ValidationException::withMessages(['account_id' => 'Select an account to register the payment.']);
        }

        $productIds = collect($validated['items'])->pluck('product_id')->unique()->values();
        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');

        if ($products->count() !== $productIds->count()) {
            throw ValidationException::withMessages(['items' => 'Some products are missing or inactive.']);
        }

        $warehouseId = (int) $validated['warehouse_id'];
        $userId = (int) $request->user()->id;

        $result = DB::transaction(function () use ($validated, $products, $warehouseId, $userId) {
            $orderNumber = $this->generateOrderNumber();

            $itemsPayload = [];
            $total = 0;

            foreach ($validated['items'] as $item) {
                $product = $products->get($item['product_id']);
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $product->price;
                $lineTotal = $quantity * $unitPrice;

                $itemsPayload[] = [
                    'product_id' => $product->id,
                    'description' => $product->description,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $lineTotal,
                ];

                $total += $lineTotal;
            }

            $order = Order::create([
                'number' => $orderNumber,
                'client_id' => $validated['client_id'] ?? null,
                'created_by' => $userId,
                'status' => $validated['paid'] ? 'completed' : 'pending',
                'channel' => $validated['channel'],
                'total' => $total,
                'ordered_at' => now(),
                'paid_at' => $validated['paid'] ? now() : null,
                'payment_method' => $validated['paid'] ? ($validated['payment_method'] ?? null) : null,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($itemsPayload as $payload) {
                $payload['order_id'] = $order->id;
                OrderItem::create($payload);

                if ($validated['paid']) {
                    $inventory = Inventory::firstOrCreate(
                        ['product_id' => $payload['product_id'], 'warehouse_id' => $warehouseId],
                        ['quantity' => 0, 'reserved' => 0, 'reorder_point' => 0]
                    );

                    $available = (float) $inventory->quantity - (float) $inventory->reserved;
                    if ($available < $payload['quantity']) {
                        throw ValidationException::withMessages([
                            'items' => 'Not enough stock for '.$products[$payload['product_id']]->name.'. Available: '.$available,
                        ]);
                    }

                    $inventory->quantity = (float) $inventory->quantity - (float) $payload['quantity'];
                    $inventory->save();

                    InventoryMovement::create([
                        'product_id' => $payload['product_id'],
                        'warehouse_from' => $warehouseId,
                        'warehouse_to' => null,
                        'quantity' => $payload['quantity'],
                        'type' => 'out',
                        'reference_type' => 'orders',
                        'reference_id' => $order->id,
                        'user_id' => $userId,
                        'notes' => 'Order '.$order->number.' payment confirmed.',
                    ]);
                }
            }

            $invoice = null;
            if ($validated['issue_invoice']) {
                $invoiceNumber = $this->generateInvoiceNumber();
                $issuedAt = now();
                $dueAt = now()->addDays(30);
                $invoiceStatus = $validated['paid'] ? 'paid' : 'issued';

                $invoice = Invoice::create([
                    'number' => $invoiceNumber,
                    'client_id' => $validated['client_id'] ?? null,
                    'created_by' => $userId,
                    'status' => $invoiceStatus,
                    'sub_total' => $total,
                    'tax' => 0,
                    'discount' => 0,
                    'total' => $total,
                    'issued_at' => $issuedAt,
                    'due_at' => $dueAt,
                    'paid_at' => $validated['paid'] ? now() : null,
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($itemsPayload as $payload) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'product_id' => $payload['product_id'],
                        'description' => $payload['description'],
                        'quantity' => $payload['quantity'],
                        'unit_price' => $payload['unit_price'],
                        'total' => $payload['total'],
                    ]);
                }
            }

            if ($validated['paid']) {
                $account = Account::findOrFail($validated['account_id']);
                $referenceType = $invoice ? 'invoices' : 'orders';
                $referenceId = $invoice ? $invoice->id : $order->id;

                $transaction = Transaction::create([
                    'account_id' => $account->id,
                    'type' => 'credit',
                    'amount' => $total,
                    'date' => now(),
                    'description' => ($invoice ? 'Invoice ' : 'Order ').($invoice ? $invoice->number : $order->number).' payment',
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                ]);

                if ($invoice) {
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'transaction_id' => $transaction->id,
                        'amount' => $total,
                        'method' => $validated['payment_method'] ?? null,
                        'reference' => 'TX'.$transaction->id,
                        'paid_at' => now(),
                    ]);
                }

                $account->increment('balance', $total);
            }

            return $order;
        });

        return response()->json([
            'message' => 'Order saved successfully.',
            'order_id' => $result->id,
        ]);
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'O'.Str::upper(Str::random(8));
        } while (Order::query()->where('number', $number)->exists());

        return $number;
    }

    private function generateInvoiceNumber(): string
    {
        do {
            $number = 'I'.Str::upper(Str::random(8));
        } while (Invoice::query()->where('number', $number)->exists());

        return $number;
    }
}
