<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoriesController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'users', 'middleware' => 'CORS'], function($router){
    Route::post('/register',[UserController::class, 'register']);
    Route::post('/username',[UserController::class, 'add_username']);
    Route::post('/login',[UserController::class, 'login']);
    Route::get('/logout',[UserController::class, 'logout']);
});

Route::group(['prefix' => 'trm', 'middleware' => ['CORS', 'auth:api']], function($router){
    Route::post('/categories',[StoriesController::class, 'getCategories']);
});

Route::get('/stories',[StoriesController::class, 'getStories']);
Route::get('/story',[StoriesController::class, 'getStory']);
Route::post('/search',[StoriesController::class, 'search']);
