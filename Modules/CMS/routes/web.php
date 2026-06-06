<?php

use Illuminate\Support\Facades\Route;
use Modules\CMS\Http\Controllers\BlogController;
use Modules\CMS\Http\Controllers\MenuController;
use Modules\CMS\Http\Controllers\MenuItemController;
use Modules\CMS\Http\Controllers\PageController;
use Modules\CMS\Http\Controllers\TeamController;
use Modules\CMS\Http\Controllers\TestimonialController;
use Modules\CMS\Http\Controllers\WebSettingController;
use Modules\CMS\Http\Controllers\BannerController;
use Modules\CMS\Http\Controllers\PageBuilderController;


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

Route::group(['prefix'=>'admin/cms','as'=>'admin.cms.','middleware'=>'auth'], function () {
    Route::resource('menus', MenuController::class)->names('menus');
    Route::resource('menu-items', MenuItemController::class)->names('menu-items');
    Route::resource('web-settings', WebSettingController::class)->names('web-settings');
    Route::resource('pages', PageController::class)->names('pages');
    Route::resource('blogs', BlogController::class)->names('blogs');
    Route::resource('testimonials', TestimonialController::class)->names('testimonials');
    Route::resource('teams', TeamController::class)->names('teams');
    Route::resource('banners', BannerController::class)->names('banners');
    Route::resource('blog-categories', \Modules\CMS\Http\Controllers\BlogCategoryController::class)->names('blog-categories');
    
    // Project Management
    Route::resource('projects', \Modules\CMS\Http\Controllers\ProjectController::class)->names('projects');

    // Page Builder Routes
    Route::resource('page-builder', PageBuilderController::class)->names('page-builder');
    Route::get('page-builder/{id}/builder', [PageBuilderController::class, 'builder'])->name('page-builder.builder');
    Route::get('page-builder/{id}/preview', [PageBuilderController::class, 'preview'])->name('page-builder.preview');
    Route::get('page-builder/{id}/export', [PageBuilderController::class, 'export'])->name('page-builder.export');
    Route::post('page-builder/import', [PageBuilderController::class, 'import'])->name('page-builder.import');

    // Forms
    Route::resource('forms', \Modules\CMS\Http\Controllers\FormController::class)->names('forms');
});

