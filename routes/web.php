<?php


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\YandexController;
use App\Http\Controllers\StoriesController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Auth\ProjectAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ObjectController;
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

require __DIR__ . '/auth.php';

Route::group(['middleware' => ['auth']], function () {
    Route::get('refresh-csrf', function(){
        return csrf_token();
    });
    Route::get('/', function () {
        return view('home', ['page' => 'ГЛАВНЯ СТРАНИЦА']);
    });
    Route::get('/yandex/auth', [YandexController::class, 'getAll']);

    Route::get('/stories/send/{project}', [StoriesController::class, 'send']);
    Route::get('/stories/edit/{story}', [StoriesController::class, 'getModal']);
    Route::post('/stories/edit/{story}', [StoriesController::class, 'edit']);
    Route::post('/stories/delete/{story}', [StoriesController::class, 'delete']);

    Route::get('/posts', [PostController::class, 'add']);
    Route::post('/posts', [PostController::class, 'send']);
    Route::post('/posts/add', [PostController::class, 'sendProjectPost']);
    Route::get('/posts/edit/{post}', [PostController::class, 'get']);
    Route::post('/posts/edit/{post}', [PostController::class, 'edit']);
    Route::post('/posts/edit/{post}/{type}', [PostController::class, 'editPoll']);
    Route::post('/posts/delete/{post}', [PostController::class, 'delete']);
    Route::get('/comment/viewed/{post}', [PostController::class, 'commentViewed']);

    Route::get('/projects/object/modal/{object}', [ProjectController::class, 'objectInfo']);
    Route::post('/projects/save/object/{object}', [ProjectController::class, 'saveObject']);
    Route::get('/projects/object/{object}/page', [ProjectController::class, 'objectPage']);

    Route::get('/projects/{project?}', [ProjectController::class, 'index'])->name('projects');
    Route::post('/projects/post/send/{post}', [PostController::class, 'sendDeferredPost']);
    Route::get('/projects/post/selectType/{project}', [ProjectController::class, 'selectPostType']);
    Route::get('/projects/page/{page}/{project}', [ProjectController::class, 'pageRender']);
    Route::post('/projects/post/getPromoLayout', [ProjectController::class, 'getPromoLayout'])->name('getPromoLayout');
    Route::get('/projects/promo/{project}', [ProjectController::class, 'promo'])->name('project_promo');
    Route::post('/projects/edit/post/{post}', [ProjectController::class, 'editPost'])->name('editPost');
    Route::post('/projects/edit/post/{post}/save', [ProjectController::class, 'saveEditPost'])->name('savePost');
    Route::post('/projects/edit/{project}', [ProjectController::class, 'edit']);
    Route::post('/projects/add_promo', [ProjectController::class, 'addPromo']);
    Route::post('/projects/group/set', [ProjectController::class, 'saveGroup']);
    Route::get('/projects/modal/add_dish', [ProjectController::class, 'modalAddDish']);
    Route::post('/projects/remove_promo', [ProjectController::class, 'removePromo']);
    Route::get('/projects/post/promo/{promo}', [ProjectController::class, 'changePromo']);
    Route::get('/projects/info/save/{project}', [ProjectController::class, 'infoSave']);
    Route::get('/projects/info/content_plan/{project}', [ProjectController::class, 'infoCpChange']);
    Route::get('/projects/modal/add-post/{project}', [PostController::class, 'modalAddToProject']);
    Route::get('/projects/create/token/{project}', [ProjectController::class, 'updateToken']);


    Route::post('/content_plan/add', [ContentPlanController::class, 'add']);
    Route::post('/content_plan/post/delete/{post}', [ContentPlanController::class, 'deletePost']);
    Route::post('/content_plan/save/{type}/{post}', [ContentPlanController::class, 'saveEditPost']);
    Route::get('/content_plan/stories/edit/{story}', [ContentPlanController::class, 'storyEditModal']);
    Route::post('/content_plan/stories/edit/{story}', [ContentPlanController::class, 'storySaveEdit']);
    Route::get('/content_plan/stories/delete/{story}', [ContentPlanController::class, 'deleteStory']);
    Route::get('/content_plan/editModal/{post}', [ContentPlanController::class, 'editModal']);
    Route::get('/content_plan/post/tags/{object?}', [ContentPlanController::class, 'tags']);
    Route::get('/content_plan/{cplan?}', [ContentPlanController::class, 'index'])->name('content_plan');
    Route::post('/content_plan/add_post', [ContentPlanController::class, 'addPost']);
    Route::post('/project/add_post', [ContentPlanController::class, 'addPost']);
    Route::post('/content_plan/add_poll', [ContentPlanController::class, 'addPoll']);
    Route::post('/content_plan/add_story', [ContentPlanController::class, 'addStory']);
    Route::post('/project/add_story', [ContentPlanController::class, 'addStory']);

    Route::get('/settings/tags', [SettingController::class, 'tagIndex']);
    Route::get('/settings/tags/object/{object}', [SettingController::class, 'objectTags']);
    Route::get('/settings/dish_type', [SettingController::class, 'dishTypeIndex']);
    Route::post('/settings/dish_type/add', [SettingController::class, 'addDishType']);

    Route::group(['middleware' => ['admin']], function () {
        Route::post('/settings/tags/update/object/{object}', [SettingController::class, 'updateTags']);
        Route::get('/employees', [UserController::class, 'get']);
        Route::get('/employees/projects/{employee}', [UserController::class, 'employeeProjects']);
        Route::post('/employees/projects/{employee}', [UserController::class, 'employeeProjectsSet']);
        Route::get('/import', [ProjectAuthController::class, 'index']);
        Route::post('/import/save', [ProjectAuthController::class, 'import']);
    });


    Route::post('/object/delete/{table}/{id}', [ObjectController::class, 'delete']);
});

Route::get('/guest/project/{project}/{token}', [ProjectController::class, 'guestAccess']);
Route::post('/guest/comment/send/{post}', [PostController::class, 'sendComment']);
