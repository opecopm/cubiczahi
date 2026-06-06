<?php

use Illuminate\Support\Facades\Route;
use Modules\IAM\Http\Controllers\PermissionController;
use Modules\IAM\Http\Controllers\PermissionGroupController;
use Modules\IAM\Http\Controllers\RoleController;
use Modules\IAM\Http\Controllers\TeamController;
use Modules\IAM\Http\Controllers\UserController;

Route::prefix('admin/iam')->name('admin.iam.')->middleware(['auth', 'verified', 'user_type:backend'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('permission-groups', PermissionGroupController::class);
    Route::resource('teams', TeamController::class);
});

Route::get('/user/set-password/{user}', [UserController::class, 'showPasswordForm'])->name('user.set-password.form')->middleware('signed');
Route::post('/user/set-password/{user}', [UserController::class, 'storePassword'])->name('user.set-password.store');
