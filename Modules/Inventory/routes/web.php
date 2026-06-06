<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\BrandController;
use Modules\Inventory\Http\Controllers\ItemCategoryController;
use Modules\Inventory\Http\Controllers\ItemController;

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

Route::group(['prefix' => 'admin/inventory', 'as' => 'admin.inventory.', 'middleware' => ['auth', 'verified', 'user_type:backend']], function () {
    Route::resource('items', ItemController::class)->names('items');
    Route::get('services', [ItemController::class, 'services'])->name('services');
    Route::resource('item-categories', ItemCategoryController::class)->names('item-categories');
    Route::resource('brands', BrandController::class)->names('brands');

});
