<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
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
Route::get('/user', [AuthController::class, 'me']);

// private posts and authors routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    
    Route::post('/tasks', [TaskController::class, 'createTask']);
    Route::get('/tasks', [TaskController::class, 'getAllTasks']);
    Route::get('/task/{id}', [TaskController::class, 'getTask']);
    Route::put('/task/{id}', [TaskController::class, 'updateTask']);
    Route::patch('/task/{id}', [TaskController::class, 'updateTask']);
    Route::delete('/task/{id}', [TaskController::class, 'deleteTask']);

    // logout 
    Route::post('/logout', [AuthController::class, 'logout']);
});

