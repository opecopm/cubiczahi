<?php

use Illuminate\Support\Facades\Route;
use Modules\Selling\Http\Controllers\SalesInvoiceController;
use Modules\Selling\Http\Controllers\SalesOrderController;

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

Route::group(['prefix' => 'admin/selling', 'as' => 'admin.selling.', 'middleware' => ['auth', 'verified', 'user_type:backend']], function () {
    Route::get('sales-orders/{id}/print', [SalesOrderController::class, 'print'])->name('sales-orders.print');
    Route::get('sales-orders/{id}', [SalesOrderController::class, 'show'])->name('sales-orders.show');
    Route::resource('sales-orders', SalesOrderController::class)->names('sales-orders');

    Route::get('sales-invoices/{id}/print', [SalesInvoiceController::class, 'print'])->name('sales-invoices.print');
    Route::resource('sales-invoices', SalesInvoiceController::class)->names('sales-invoices');
});
