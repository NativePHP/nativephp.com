<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShowDocumentationController;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs/{version}/{page?}', ShowDocumentationController::class)
    ->where('page', '(.*)')
    ->where('version', '[0-9]+');

// Forward unversioned requests to the latest version
Route::get('/docs/{page?}', function ($page = null) {
    $latestVersion = '1';

    $referer = request()->header('referer');

    $version = Str::before(ltrim(Str::after($referer, url('/docs/')), '/'), '/') ?: $latestVersion;

    return redirect("/docs/{$version}/{$page}");
})->name('docs')->where('page', '.*');
