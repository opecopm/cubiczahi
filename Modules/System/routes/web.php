<?php

use Illuminate\Support\Facades\Route;
use Modules\System\Http\Controllers\MenuController;
use Modules\System\Http\Controllers\MenuItemController;
use Modules\System\Http\Controllers\WorkflowController;

Route::prefix('admin/system')->name('admin.system.')->middleware(['auth', 'verified', 'user_type:backend'])->group(function () {
    Route::resource('menus', MenuController::class);
    Route::resource('menu-items', MenuItemController::class);
    Route::resource('workflows', WorkflowController::class);
});
