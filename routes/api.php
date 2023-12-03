<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// signup and login
Route::post('/signup', [AuthController::class, 'sign_up']);
Route::post('/login', [AuthController::class, 'login']);

// public post routes
Route::get('/posts/search/{title}', [PostController::class, 'search']);
Route::get('/post/author/{id}', [PostController::class, 'get_author']);

// public author routes
// Route::get('/authors/search/{name}', [AuthorController::class, 'search']);
// Route::get('/author/posts/{id}', [AuthorController::class, 'get_posts']);

// private posts and authors routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    // private post routes
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    // private author routes
    // Route::post('/authors', [AuthorController::class, 'store']);
    // Route::put('/authors/{id}', [AuthorController::class, 'update']);
    // Route::delete('/authors/{id}', [AuthorController::class, 'destroy']); 

    // logout 
    Route::post('/logout', [AuthController::class, 'logout']);
});

