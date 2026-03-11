<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SeedSalesData extends Command
{
    protected $signature = 'pos:seed-sales
        {--count=300 : Number of orders to generate}
        {--days=90 : Spread orders across the last N days}
        {--max-items=5 : Max line items per order}
        {--issue-invoice=0.6 : Ratio (0-1) of orders that get invoices}
        {--paid=0.8 : Ratio (0-1) of orders marked as paid}
        {--warehouse= : Force warehouse id}
        {--account= : Force account id for payments}
        {--client= : Force client id}
        {--user= : Force user id}';

    protected $description = 'Generate simulated sales orders, optional invoices, inventory movements, and transactions.';

    public function handle(): int
    {
        $count = (int) $this->option('count');
        $days = max(1, (int) $this->option('days'));
        $maxItems = max(1, (int) $this->option('max-items'));
        $invoiceRatio = $this->clampRatio($this->option('issue-invoice'));
        $paidRatio = $this->clampRatio($this->option('paid'));

        $products = Product::query()->where('active', true)->get();
        $warehouses = Warehouse::query()->get();
        $accounts = Account::query()->get();
        $users = User::query()->get();
        $clients = Client::query()->get();

        if ($products->isEmpty() || $warehouses->isEmpty() || $accounts->isEmpty() || $users->isEmpty()) {
            $this->error('Missing required data. Ensure products, warehouses, accounts, and users exist.');
            return 1;
        }

        $forcedWarehouse = $this->option('warehouse');
        $forcedAccount = $this->option('account');
        $forcedClient = $this->option('client');
        $forcedUser = $this->option('user');

        $this->info('Seeding sales data...');

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            DB::transaction(function () use (
                $days,
                $maxItems,
                $invoiceRatio,
                $paidRatio,
                $products,
                $warehouses,
                $accounts,
                $users,
                $clients,
                $forcedWarehouse,
                $forcedAccount,
                $forcedClient,
                $forcedUser
            ) {
                $orderDate = Carbon::now()->subDays(random_int(0, $days - 1))->setTime(
                    random_int(8, 18),
                    random_int(0, 59),
                    random_int(0, 59)
                );

                $paid = (mt_rand() / mt_getrandmax()) < $paidRatio;
                $issueInvoice = (mt_rand() / mt_getrandmax()) < $invoiceRatio;

                $warehouse = $forcedWarehouse ? $warehouses->firstWhere('id', (int) $forcedWarehouse) : $warehouses->random();
                $account = $forcedAccount ? $accounts->firstWhere('id', (int) $forcedAccount) : $accounts->random();
                $user = $forcedUser ? $users->firstWhere('id', (int) $forcedUser) : $users->random();
                $client = $forcedClient ? $clients->firstWhere('id', (int) $forcedClient) : ($clients->isEmpty() ? null : $clients->random());
                $clientId = $client ? $client->id : null;

                $order = Order::create([
                    'number' => $this->generateOrderNumber(),
                    'client_id' => $clientId,
                    'created_by' => $user->id,
                    'status' => $paid ? 'completed' : 'pending',
                    'channel' => (mt_rand(0, 1) ? 'online' : 'physical'),
                    'total' => 0,
                    'ordered_at' => $orderDate,
                    'paid_at' => $paid ? $orderDate->copy()->addMinutes(random_int(5, 240)) : null,
                    'payment_method' => $paid ? ['cash', 'card', 'transfer'][random_int(0, 2)] : null,
                    'notes' => 'Seeded order',
                ]);

                $itemsCount = random_int(1, $maxItems);
                $lineItems = $products->random($itemsCount);
                $total = 0;
                $orderItemsPayload = [];

                foreach ($lineItems as $product) {
                    $quantity = (float) random_int(1, 8);
                    $unitPrice = (float) $product->price;
                    $lineTotal = $quantity * $unitPrice;

                    $orderItemPayload = [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'description' => $product->description,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total' => $lineTotal,
                    ];

                    OrderItem::create($orderItemPayload);
                    $orderItemsPayload[] = $orderItemPayload;

                    $total += $lineTotal;

                    if ($paid) {
                        $inventory = Inventory::firstOrCreate(
                            ['product_id' => $product->id, 'warehouse_id' => $warehouse->id],
                            ['quantity' => 0, 'reserved' => 0, 'reorder_point' => 0]
                        );

                        $available = (float) $inventory->quantity - (float) $inventory->reserved;
                        if ($available < $quantity) {
                            $replenishQty = (float) max(10, $quantity * random_int(3, 8));
                            $inventory->quantity = (float) $inventory->quantity + $replenishQty;
                            $inventory->save();

                            DB::table('inventory_movements')->insert([
                                'product_id' => $product->id,
                                'warehouse_from' => null,
                                'warehouse_to' => $warehouse->id,
                                'quantity' => $replenishQty,
                                'type' => 'in',
                                'reference_type' => 'seed-reorder',
                                'reference_id' => $order->id,
                                'user_id' => $user->id,
                                'notes' => 'Seeded restock before sale.',
                                'created_at' => $orderDate->copy()->subHours(random_int(2, 48)),
                                'updated_at' => $orderDate->copy()->subHours(random_int(2, 48)),
                            ]);
                        }

                        $inventory->quantity = (float) $inventory->quantity - $quantity;
                        $inventory->save();

                        DB::table('inventory_movements')->insert([
                            'product_id' => $product->id,
                            'warehouse_from' => $warehouse->id,
                            'warehouse_to' => null,
                            'quantity' => $quantity,
                            'type' => 'out',
                            'reference_type' => 'orders',
                            'reference_id' => $order->id,
                            'user_id' => $user->id,
                            'notes' => 'Seeded sale.',
                            'created_at' => $orderDate,
                            'updated_at' => $orderDate,
                        ]);
                    }
                }

                $order->update(['total' => $total]);

                $invoice = null;
                if ($issueInvoice) {
                    $invoice = Invoice::create([
                        'number' => $this->generateInvoiceNumber(),
                        'client_id' => $clientId,
                        'created_by' => $user->id,
                        'status' => $paid ? 'paid' : 'issued',
                        'sub_total' => $total,
                        'tax' => 0,
                        'discount' => 0,
                        'total' => $total,
                        'issued_at' => $orderDate,
                        'due_at' => $orderDate->copy()->addDays(30),
                        'paid_at' => $paid ? $orderDate->copy()->addMinutes(random_int(5, 240)) : null,
                        'notes' => 'Seeded invoice',
                    ]);

                    foreach ($orderItemsPayload as $item) {
                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'product_id' => $item['product_id'],
                            'description' => $item['description'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'total' => $item['total'],
                        ]);
                    }
                }

                if ($paid) {
                    $referenceType = $invoice ? 'invoices' : 'orders';
                    $referenceId = $invoice ? $invoice->id : $order->id;

                    $transaction = Transaction::create([
                        'account_id' => $account->id,
                        'type' => 'credit',
                        'amount' => $total,
                        'date' => $orderDate,
                        'description' => ($invoice ? 'Invoice ' : 'Order ').($invoice ? $invoice->number : $order->number).' payment',
                        'reference_type' => $referenceType,
                        'reference_id' => $referenceId,
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ]);

                    if ($invoice) {
                        Payment::create([
                            'invoice_id' => $invoice->id,
                            'transaction_id' => $transaction->id,
                            'amount' => $total,
                            'method' => $order->payment_method,
                            'reference' => 'TX'.$transaction->id,
                            'paid_at' => $orderDate,
                            'created_at' => $orderDate,
                            'updated_at' => $orderDate,
                        ]);
                    }

                    $account->increment('balance', $total);
                }
            });

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Sales data seeded successfully.');

        return 0;
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

    private function clampRatio($value): float
    {
        $ratio = (float) $value;
        if ($ratio < 0) {
            return 0.0;
        }
        if ($ratio > 1) {
            return 1.0;
        }

        return $ratio;
    }
}
