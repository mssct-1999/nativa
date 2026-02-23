<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        // Roles
        $roles = [
            ['name' => 'admin', 'label' => 'Administrator', 'description' => 'Full access'],
            ['name' => 'manager', 'label' => 'Manager', 'description' => 'Manage teams and operations'],
            ['name' => 'staff', 'label' => 'Staff', 'description' => 'Regular user']
        ];
        foreach ($roles as $r) {
            DB::table('roles')->updateOrInsert(['name' => $r['name']], array_merge($r, ['created_at' => now(), 'updated_at' => now()]));
        }

        // Permissions (simple examples)
        $perms = ['manage_users','manage_products','manage_orders','view_reports','manage_inventory'];
        foreach ($perms as $p) {
            DB::table('permissions')->updateOrInsert(['name' => $p], ['label' => ucfirst(str_replace('_',' ', $p)), 'created_at'=>now(), 'updated_at'=>now()]);
        }

        // Users
        $users = [];
        $users[] = [
            'name' => 'Admin User',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        for ($i=1;$i<=4;$i++) {
            $users[] = [
                'name' => $faker->name,
                'email' => "user{$i}@example.test",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $userIds = [];
        foreach ($users as $u) {
            $userIds[] = DB::table('users')->insertGetId($u);
        }

        // Assign roles: first user = admin, second = manager, others staff
        if (!empty($userIds)) {
            DB::table('role_user')->updateOrInsert(['role_id' => DB::table('roles')->where('name','admin')->value('id'), 'user_id' => $userIds[0]], ['created_at'=>now(),'updated_at'=>now()]);
            if (isset($userIds[1])) DB::table('role_user')->updateOrInsert(['role_id' => DB::table('roles')->where('name','manager')->value('id'), 'user_id' => $userIds[1]], ['created_at'=>now(),'updated_at'=>now()]);
            for ($i=2;$i<count($userIds);$i++) {
                DB::table('role_user')->updateOrInsert(['role_id' => DB::table('roles')->where('name','staff')->value('id'), 'user_id' => $userIds[$i]], ['created_at'=>now(),'updated_at'=>now()]);
            }
        }

        // Clients & Prospects
        $clientIds = [];
        for ($i=0;$i<10;$i++) {
            $id = DB::table('clients')->insertGetId([
                'company_name' => $faker->company,
                'contact_name' => $faker->name,
                'email' => $faker->companyEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->streetAddress,
                'city' => $faker->city,
                'zipcode' => $faker->postcode,
                'country' => $faker->country,
                'notes' => $faker->sentence,
                'created_by' => $userIds[array_rand($userIds)],
                'created_at'=>now(),'updated_at'=>now()
            ]);
            $clientIds[] = $id;
        }

        $prospectIds = [];
        for ($i=0;$i<6;$i++) {
            $prospectIds[] = DB::table('prospects')->insertGetId([
                'company_name' => $faker->company,
                'contact_name' => $faker->name,
                'email' => $faker->companyEmail,
                'phone' => $faker->phoneNumber,
                'source' => $faker->randomElement(['web','trade_show','referral']),
                'notes' => $faker->sentence,
                'assigned_to' => $userIds[array_rand($userIds)],
                'created_at'=>now(),'updated_at'=>now()
            ]);
        }

        // Products
        $productIds = [];
        for ($i=1;$i<=12;$i++) {
            $productIds[] = DB::table('products')->insertGetId([
                'sku' => 'SKU'.str_pad($i,4,'0',STR_PAD_LEFT),
                'name' => $faker->word . ' Product ' . $i,
                'description' => $faker->sentence,
                'price' => $faker->randomFloat(2,10,1000),
                'cost' => $faker->randomFloat(2,5,500),
                'taxable' => 1,
                'active' => 1,
                'created_at'=>now(),'updated_at'=>now()
            ]);
        }

        // Warehouses
        $warehouseIds = [];
        $warehouseIds[] = DB::table('warehouses')->insertGetId(['name' => 'Main Warehouse','code'=>'MAIN','location'=>'Head Office','contact'=>$faker->name,'notes'=>'Primary','created_at'=>now(),'updated_at'=>now()]);
        $warehouseIds[] = DB::table('warehouses')->insertGetId(['name' => 'Secondary Warehouse','code'=>'WH2','location'=>'Remote Site','contact'=>$faker->name,'notes'=>'Overflow','created_at'=>now(),'updated_at'=>now()]);

        // Inventories
        foreach ($productIds as $pid) {
            foreach ($warehouseIds as $wid) {
                DB::table('inventories')->updateOrInsert(
                    ['product_id'=>$pid,'warehouse_id'=>$wid],
                    ['quantity' => rand(0,200),'reserved'=>rand(0,20),'reorder_point'=>rand(5,30),'created_at'=>now(),'updated_at'=>now()]
                );
            }
        }

        // Create some quotes, orders, invoices with items
        for ($q=1;$q<=5;$q++) {
            $client = $clientIds[array_rand($clientIds)];
            $quoteId = DB::table('quotes')->insertGetId([
                'number' => 'Q'.Str::upper(Str::random(6)).$q,
                'client_id' => $client,
                'created_by' => $userIds[array_rand($userIds)],
                'status' => $faker->randomElement(['draft','sent','accepted']),
                'sub_total'=>0,'tax'=>0,'discount'=>0,'total'=>0,'valid_until'=>now()->addDays(30),'notes'=>$faker->sentence,'created_at'=>now(),'updated_at'=>now()
            ]);

            $lines = rand(1,4);
            $sub = 0;
            for ($l=0;$l<$lines;$l++) {
                $pid = $productIds[array_rand($productIds)];
                $qty = rand(1,10);
                $price = DB::table('products')->where('id',$pid)->value('price');
                $total = $qty * $price;
                DB::table('quote_items')->insert(['quote_id'=>$quoteId,'product_id'=>$pid,'description'=>'','quantity'=>$qty,'unit_price'=>$price,'total'=>$total,'created_at'=>now(),'updated_at'=>now()]);
                $sub += $total;
            }
            DB::table('quotes')->where('id',$quoteId)->update(['sub_total'=>$sub,'total'=>$sub,'updated_at'=>now()]);

            // Create corresponding order and invoice for some
            if (rand(0,1)) {
                $orderId = DB::table('orders')->insertGetId(['number'=>'O'.Str::upper(Str::random(6)).$q,'client_id'=>$client,'created_by'=>$userIds[array_rand($userIds)],'status'=>'pending','total'=>$sub,'ordered_at'=>now(),'notes'=>'Auto-generated from quote','created_at'=>now(),'updated_at'=>now()]);
                // copy quote items to order_items
                $items = DB::table('quote_items')->where('quote_id',$quoteId)->get();
                foreach ($items as $it) {
                    DB::table('order_items')->insert(['order_id'=>$orderId,'product_id'=>$it->product_id,'description'=>$it->description,'quantity'=>$it->quantity,'unit_price'=>$it->unit_price,'total'=>$it->total,'created_at'=>now(),'updated_at'=>now()]);
                }

                if (rand(0,1)) {
                    $invId = DB::table('invoices')->insertGetId(['number'=>'I'.Str::upper(Str::random(6)).$q,'client_id'=>$client,'quote_id'=>$quoteId,'created_by'=>$userIds[array_rand($userIds)],'status'=>'issued','sub_total'=>$sub,'tax'=>0,'discount'=>0,'total'=>$sub,'issued_at'=>now(),'due_at'=>now()->addDays(30),'notes'=>'Auto invoice','created_at'=>now(),'updated_at'=>now()]);
                    foreach (DB::table('order_items')->where('order_id',$orderId)->get() as $it) {
                        DB::table('invoice_items')->insert(['invoice_id'=>$invId,'product_id'=>$it->product_id,'description'=>$it->description,'quantity'=>$it->quantity,'unit_price'=>$it->unit_price,'total'=>$it->total,'created_at'=>now(),'updated_at'=>now()]);
                    }
                    // optionally mark a payment
                    if (rand(0,1)) {
                        $payAmount = $sub;
                        // create a transaction and payment
                        $accId = DB::table('accounts')->insertGetId(['name'=>'Bank Account','type'=>'bank','currency'=>'USD','balance'=>$payAmount,'notes'=>'Seeded account','created_at'=>now(),'updated_at'=>now()]);
                        $transId = DB::table('transactions')->insertGetId(['account_id'=>$accId,'type'=>'credit','amount'=>$payAmount,'date'=>now(),'description'=>'Payment for invoice '.$invId,'reference_type'=>'invoices','reference_id'=>$invId,'created_at'=>now(),'updated_at'=>now()]);
                        DB::table('payments')->insert(['invoice_id'=>$invId,'transaction_id'=>$transId,'amount'=>$payAmount,'method'=>'bank_transfer','reference'=>'TX'.$transId,'paid_at'=>now(),'created_at'=>now(),'updated_at'=>now()]);
                    }
                }
            }
        }

        // Create some accounts if none
        if (DB::table('accounts')->count() < 1) {
            DB::table('accounts')->insert(['name'=>'Main Bank','type'=>'bank','currency'=>'USD','balance'=>10000,'notes'=>'Seed seed','created_at'=>now(),'updated_at'=>now()]);
            DB::table('accounts')->insert(['name'=>'Cash','type'=>'cash','currency'=>'USD','balance'=>500,'notes'=>'Seed seed','created_at'=>now(),'updated_at'=>now()]);
        }

        // Employees (link some users)
        foreach ($userIds as $uid) {
            DB::table('employees')->updateOrInsert(['user_id'=>$uid],['employee_number'=>'EMP'.str_pad($uid,4,'0',STR_PAD_LEFT),'hire_date'=>now()->subYears(rand(0,10)),'position'=>$faker->jobTitle,'department'=>$faker->randomElement(['Sales','Production','HR','Finance']),'salary'=>$faker->randomFloat(2,20000,80000),'created_at'=>now(),'updated_at'=>now()]);
        }

        // Production sample
        foreach (array_slice($productIds,0,3) as $pid) {
            $ppId = DB::table('production_plans')->insertGetId(['name'=>'Plan for product '.$pid,'product_id'=>$pid,'start_date'=>now(),'end_date'=>now()->addDays(7),'quantity'=>rand(10,200),'status'=>'planned','notes'=>'Seeded plan','created_at'=>now(),'updated_at'=>now()]);
            $woId = DB::table('work_orders')->insertGetId(['production_plan_id'=>$ppId,'order_number'=>'WO'.Str::upper(Str::random(6)),'product_id'=>$pid,'quantity'=>rand(10,200),'status'=>'open','created_at'=>now(),'updated_at'=>now()]);
            DB::table('quality_checks')->insert(['work_order_id'=>$woId,'inspector_id'=>null,'result'=>'pass','notes'=>'Seed check','checked_at'=>now(),'created_at'=>now(),'updated_at'=>now()]);
        }
    }
}
