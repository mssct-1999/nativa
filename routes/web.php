<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ShopCatalogController;
use App\Http\Controllers\ShopCustomerController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\ShopCartController;
use App\Http\Controllers\ShopCheckoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🏠 HOME (storefront landing)
Route::get('/', [StorefrontController::class, 'index'])->name('shop.index');


// 🔐 ADMIN AREA (IMPORTANT: comes BEFORE dynamic shop routes)
Route::middleware(['auth', 'role:admin'])->group(function() {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CLIENTS
    Route::get('clients/list', [ClientController::class, 'list'])->name('clients.list');
    Route::resource('clients', ClientController::class);

    // PRODUCTS
    Route::get('products/list', [ProductController::class, 'list'])->name('products.list');
    Route::resource('products', ProductController::class);

    // ORDERS 
    Route::get('orders/list', [OrderController::class, 'list'])->name('orders.list');
    Route::patch('orders/{order}/mark-shipped', [OrderController::class, 'markShipped'])->name('orders.mark-shipped');
    Route::resource('orders', OrderController::class);

    // INVOICES 
    Route::get('invoices/list', [InvoiceController::class, 'list'])->name('invoices.list');
    Route::resource('invoices', InvoiceController::class);

    // INVENTORY 
    Route::get('inventory/list', [InventoryController::class, 'list'])->name('inventory.list');
    Route::resource('inventory', InventoryController::class);

    // WAREHOUSES
    Route::get('warehouses/list', [WarehouseController::class, 'list'])->name('warehouses.list');
    Route::resource('warehouses', WarehouseController::class);

    // 🏪 SHOPS (ADMIN CRUD)
    Route::resource('shops', ShopController::class)->except(['show']);

    // SHOP CATALOG
    Route::get('shops/{shop}/catalog', [ShopCatalogController::class, 'index'])->name('shops.catalog');
    Route::post('shops/{shop}/catalog', [ShopCatalogController::class, 'store'])->name('shops.catalog.store');

    // SHOP CUSTOMERS (CRM)
    Route::get('shop-customers', [ShopCustomerController::class, 'index'])->name('shop-customers.index');
    Route::get('shop-customers/{shopCustomer}', [ShopCustomerController::class, 'show'])->name('shop-customers.show');
    Route::get('shop-customers/{shopCustomer}/edit', [ShopCustomerController::class, 'edit'])->name('shop-customers.edit');
    Route::patch('shop-customers/{shopCustomer}', [ShopCustomerController::class, 'update'])->name('shop-customers.update');

    // EMPLOYEES 
    Route::get('employees/list', [EmployeeController::class, 'list'])->name('employees.list');
    Route::patch('employees/{employee}/manager', [EmployeeController::class, 'updateManager'])->name('employees.manager.update');
    Route::resource('employees', EmployeeController::class);

    // PAYROLLS
    Route::get('payrolls/list', [PayrollController::class, 'list'])->name('payrolls.list');
    Route::get('payrolls/export/pdf', [PayrollController::class, 'exportPdf'])->name('payrolls.export.pdf');
    Route::resource('payrolls', PayrollController::class);

    // POS + TRANSACTIONS
    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('pos/lookup', [PosController::class, 'lookup'])->name('pos.lookup');
    Route::get('pos/search', [PosController::class, 'search'])->name('pos.search');
    Route::post('pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.list');

    // SETTINGS
    Route::post('settings/locale', function (\Illuminate\Http\Request $request) {
        $locale = $request->input('locale');
        $supported = ['en', 'fr', 'pt'];

        if (is_string($locale) && in_array($locale, $supported, true)) {
            $request->session()->put('locale', $locale);
        }

        return redirect()->back();
    })->name('settings.locale');

    // PROFILE
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
});


// 🛒 STOREFRONT (PUBLIC) — MUST COME LAST

Route::get('/shops/{shop:slug}/products/{product}', [StorefrontController::class, 'product'])->name('shop.product');

Route::get('/shops/{shop:slug}/cart', [ShopCartController::class, 'index'])->name('shop.cart');
Route::post('/shops/{shop:slug}/cart', [ShopCartController::class, 'store'])->name('shop.cart.store');
Route::patch('/shops/{shop:slug}/cart', [ShopCartController::class, 'update'])->name('shop.cart.update');
Route::delete('/shops/{shop:slug}/cart/{product}', [ShopCartController::class, 'destroy'])->name('shop.cart.destroy');

Route::get('/shops/{shop:slug}/checkout', [ShopCheckoutController::class, 'create'])
    ->middleware(['auth', 'role:customer'])
    ->name('shop.checkout');

Route::post('/shops/{shop:slug}/checkout', [ShopCheckoutController::class, 'store'])
    ->middleware(['auth', 'role:customer'])
    ->name('shop.checkout.store');

// 🔥 VERY IMPORTANT: keep this LAST
Route::get('/shops/{shop:slug}', [StorefrontController::class, 'show'])
    ->where('shop', '^(?!create$|edit$).*$')
    ->name('shop.show');


require __DIR__.'/auth.php';