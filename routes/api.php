<?php

use App\Http\Controllers\Api\LicenseController;
use App\Http\Controllers\Api\PluginAccessController;
use App\Http\Controllers\Api\TemporaryLinkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Plugin Access Validation (for Satis/Composer authentication)
|--------------------------------------------------------------------------
|
| These endpoints use HTTP Basic Auth (email:plugin_license_key) to validate
| user credentials and return their accessible plugins.
|
*/

Route::middleware('auth.api_key')->group(function () {
    Route::prefix('plugins')->name('api.plugins.')->group(function () {
        Route::get('/access', [PluginAccessController::class, 'index'])->name('access');
        Route::get('/access/{vendor}/{package}', [PluginAccessController::class, 'checkAccess'])->name('access.check');
    });

    Route::post('/licenses', [LicenseController::class, 'store']);
    Route::get('/licenses/{key}', [LicenseController::class, 'show']);
    Route::get('/licenses', [LicenseController::class, 'index']);
    Route::post('/temp-links', [TemporaryLinkController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
});
