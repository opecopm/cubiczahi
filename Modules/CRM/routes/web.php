<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\CustomerController;
use Modules\CRM\Http\Controllers\CustomerGroupController;
use Modules\CRM\Http\Controllers\TerritoryController;

Route::group(['prefix' => 'admin/crm', 'as' => 'admin.crm.', 'middleware' => ['auth', 'verified', 'user_type:backend']], function () {
    Route::resource('customers', CustomerController::class);
    Route::get('customers/{customer}/orders', [CustomerController::class, 'orders'])->name('customers.orders');
    Route::get('customers/{customer}/orders/{order}', [CustomerController::class, 'showOrder'])->name('customers.orders.show');
    Route::get('customers/{customer}/invoices', [CustomerController::class, 'invoices'])->name('customers.invoices');
    Route::get('customers/{customer}/invoices/{invoice}', [CustomerController::class, 'showInvoice'])->name('customers.invoices.show');
    Route::resource('customer-groups', CustomerGroupController::class);
});
