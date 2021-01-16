<?php


use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ContentPlanController;
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

Route::get('/posts', [PostController::class, 'addPost']);
Route::post('/posts', [PostController::class, 'sendPost']);
Route::post('/posts/add', [PostController::class, 'sendProjectPost']);

Route::get('/projects', [ProjectController::class, 'index']);
Route::post('/projects/edit/{project}', [ProjectController::class, 'edit']);
Route::get('/projects/info/{project?}', [ProjectController::class, 'info']);
Route::post('/projects/add_promo', [ProjectController::class, 'addPromo']);
Route::post('/projects/remove_promo', [ProjectController::class, 'removePromo']);

Route::get('/promo', [PromoController::class, 'index']);
Route::post('/promo', [PromoController::class, 'saveLocked']);
Route::get('/promo/{promo}/{project}', [PromoController::class, 'getCurrent']);
Route::get('/promo/for_project', [PromoController::class, 'availablePromo']);
Route::get('/promo/edit/{promo}', [PromoController::class, 'index']);

Route::post('/content_plan/add', [ContentPlanController::class, 'add']);
Route::get('/content_plan/{cplan?}', [ContentPlanController::class, 'index'])->name('content_plan');
Route::post('/content_plan/add_post', [ContentPlanController::class, 'addPost']);
