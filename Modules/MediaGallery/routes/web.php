<?php

use Illuminate\Support\Facades\Route;
use Modules\MediaGallery\Http\Controllers\MediaGalleryController;

Route::group([
    'prefix' => 'admin/media-gallery',
    'as' => 'admin.mediagallery.',
    'middleware' => ['auth', 'verified', 'user_type:backend'],
], function () {
    Route::get('/', [MediaGalleryController::class, 'index'])->name('media-assets.index');
    Route::get('/{mediaAsset}', [MediaGalleryController::class, 'show'])->name('media-assets.show');
    Route::get('/{mediaAsset}/preview', [MediaGalleryController::class, 'preview'])->name('media-assets.preview');
    Route::get('/{mediaAsset}/download', [MediaGalleryController::class, 'download'])->name('media-assets.download');
});
