<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShowDocumentationController;

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
Route::redirect('/docs', '/docs/1')->name('docs');
Route::get('/docs/{version}/{page?}', ShowDocumentationController::class)->where('page', '(.*)');
