<?php

use Illuminate\Support\Facades\Route;
use Modules\IAM\Http\Controllers\API\V1\AuthController;
use Modules\IAM\Http\Controllers\API\V1\UserProjectsController;
use Modules\IAM\Http\Controllers\IAMController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('iam.login');

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('iam.logout');
        Route::get('me', [AuthController::class, 'me'])->name('iam.me');
        Route::get('user-projects', [UserProjectsController::class, 'index'])->name('iam.user-projects.index');
        Route::apiResource('iam', IAMController::class)->names('iam');
    });
});
