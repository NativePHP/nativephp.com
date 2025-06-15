<?php

use App\Http\Controllers\ShowDocumentationController;
use Illuminate\Support\Facades\Route;
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

Route::redirect('/newsletter', 'https://simonhamp.mailcoach.app/nativephp');
Route::redirect('/sponsor', '/docs/1/getting-started/sponsoring');
Route::redirect('/phpverse-2025', 'https://lp.jetbrains.com/phpverse-2025');

Route::view('/', 'welcome')->name('welcome');
Route::view('/blog', 'blog')->name('blog');
Route::view('/article', 'article')->name('article');
Route::view('mobile', 'early-adopter')->name('early-adopter');
Route::redirect('ios', 'mobile');
Route::redirect('t-shirt', 'mobile');
Route::redirect('tshirt', 'mobile');
Route::view('privacy-policy', 'privacy-policy');
Route::view('terms-of-service', 'terms-of-service');
Route::view('partners', 'partners')->name('partners');

Route::redirect('/docs/{version}/{page?}', '/docs/mobile/{version}/{page?}')
    ->where('page', '(.*)')
    ->where('version', '[0-9]+');

Route::get('/docs/{platform}/{version}/{page?}', ShowDocumentationController::class)
    ->where('page', '(.*)')
    ->where('platform', '[a-z]+')
    ->where('version', '[0-9]+');

// Forward unversioned requests to the latest version
Route::get('/docs/{page?}', function ($page = null) {
    $version = session('viewing_docs_version', '1');

    $referer = request()->header('referer');

    // If coming from elsewhere in the docs, match the current version being viewed
    if (
        ! session()->has('viewing_docs_version')
        && parse_url($referer, PHP_URL_HOST) === parse_url(url('/'), PHP_URL_HOST)
        && str($referer)->contains('/docs/')
    ) {
        $version = Str::before(ltrim(Str::after($referer, url('/docs/')), '/'), '/');
    }

    return redirect("/docs/{$version}/{$page}");
})->name('docs')->where('page', '.*');

Route::get('/order/{checkoutSessionId}', App\Livewire\OrderSuccess::class)->name('order.success');
