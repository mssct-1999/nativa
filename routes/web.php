<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\EmployeeController;
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
    Route::resource('clients', ClientController::class);

    // PRODUCTS
    Route::resource('products', ProductController::class);

    // ORDERS 
    Route::resource('orders', OrderController::class);

    // INVOICES 
    Route::resource('invoices', InvoiceController::class);

    // INVENTORY 
    Route::resource('inventory', InventoryController::class);

    // EMPLOYEES 
    Route::resource('employees', EmployeeController::class);
});

require __DIR__.'/auth.php';
