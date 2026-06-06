<?php

use Illuminate\Support\Facades\Route;
use Modules\Business\Http\Controllers\BusinessController;
use Modules\Business\Http\Controllers\BusinessPartnerController;
use Modules\Business\Http\Controllers\CompanyController;
use Modules\Business\Http\Controllers\CurrencyController;
use Modules\Business\Http\Controllers\LocationController;
use Modules\Business\Http\Controllers\SponsorController;
use Modules\Business\Http\Controllers\TaxController;

Route::prefix('admin/business')->name('admin.business.')->middleware(['auth', 'verified', 'user_type:backend'])->group(function () {
    Route::resource('companies', CompanyController::class);
    Route::resource('business-partners', BusinessPartnerController::class);
    Route::get('settings', [BusinessController::class, 'businessSettings'])->name('settings');
    Route::resource('sponsors', SponsorController::class);
    Route::resource('taxes', TaxController::class);
    Route::resource('currencies', CurrencyController::class);
    Route::resource('locations', LocationController::class);
});
