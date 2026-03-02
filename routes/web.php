<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[DashboardController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function() {

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

    // EMPLOYEES 
    Route::get('employees/list', [EmployeeController::class, 'list'])->name('employees.list');
    Route::patch('employees/{employee}/manager', [EmployeeController::class, 'updateManager'])->name('employees.manager.update');
    Route::resource('employees', EmployeeController::class);

    // PAYROLLS
    Route::get('payrolls/list', [PayrollController::class, 'list'])->name('payrolls.list');
    Route::get('payrolls/export/pdf', [PayrollController::class, 'exportPdf'])->name('payrolls.export.pdf');
    Route::resource('payrolls', PayrollController::class);
});

require __DIR__.'/auth.php';
