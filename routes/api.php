<?php

use App\Http\Controllers\Api\LicenseController;
use App\Http\Controllers\Api\TemporaryLinkController;
use App\Http\Controllers\McpController;
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

// MCP Server routes (no session/cookies - fixes CSRF 419 errors)
Route::prefix('mcp')->group(function () {
    Route::get('sse', [McpController::class, 'sse'])->name('mcp.sse');
    Route::post('message', [McpController::class, 'message'])->name('mcp.message');
    Route::get('health', [McpController::class, 'health'])->name('mcp.health');

    // REST API endpoints
    Route::get('search', [McpController::class, 'searchApi'])->name('mcp.api.search');
    Route::get('page/{platform}/{version}/{section}/{slug}', [McpController::class, 'pageApi'])->name('mcp.api.page');
    Route::get('apis/{platform}/{version}', [McpController::class, 'apisApi'])->name('mcp.api.apis');
    Route::get('navigation/{platform}/{version}', [McpController::class, 'navigationApi'])->name('mcp.api.navigation');
});

Route::middleware('auth.api_key')->group(function () {
    Route::post('/licenses', [LicenseController::class, 'store']);
    Route::get('/licenses/{key}', [LicenseController::class, 'show']);
    Route::get('/licenses', [LicenseController::class, 'index']);
    Route::post('/temp-links', [TemporaryLinkController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
});
