<?php

use Illuminate\Support\Facades\Route;
use Modules\Selling\Http\Controllers\SellingController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sellings', SellingController::class)->names('selling');
});
