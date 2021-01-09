<?php


use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ProjectController;
use App\Http\Requests\PostRequest;

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

Route::get('/', function () {
    return view('home', ['page' => 'ГЛАВНЯ СТРАНИЦА']);
});

Route::get('/post', [PostController::class, 'addPost']);

Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/info', [ProjectController::class, 'info']);

Route::post('/post', [PostController::class, 'sendPost']);

Route::get('/promo', [PromoController::class, 'index']);
Route::post('/promo', [PromoController::class, 'saveLocked']);
