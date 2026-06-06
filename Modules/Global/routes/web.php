<?php

use Illuminate\Support\Facades\Route;
use Modules\Global\Http\Controllers\CustomFieldController;
use Modules\Global\Http\Controllers\GeneralDocumentTypeController;
use Modules\Global\Http\Controllers\LanguageController;
use Modules\Global\Http\Controllers\ReferenceSchemaController;

Route::prefix('admin/global')->name('admin.global.')->middleware(['auth', 'verified', 'user_type:backend'])->group(function () {
    Route::resource('reference-schemas', ReferenceSchemaController::class);
    Route::resource('custom-fields', CustomFieldController::class);
    Route::resource('document-types', GeneralDocumentTypeController::class);
    Route::resource('languages', LanguageController::class);
});
