<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
    // $role = Role::create(['name' => 'user']);
    // $permission = Permission::create(['name' => 'delete tasks',]);
    // $permission = Permission::create(['name' => 'update tasks',]);
    // $permission = Permission::create(['name' => 'get tasks',]);
    // $permission = Permission::create(['name' => 'create tasks',]);
    return view('welcome');
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    // logout 
    Route::post('/logout', [AuthController::class, 'logout']);
});