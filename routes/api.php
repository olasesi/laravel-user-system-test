<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\AdminAuthController;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::middleware(['api', 'prevent.authenticated'])->group(function () {

    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/admin-login', [AdminAuthController::class, 'adminLogin']);
   
});


Route::middleware(['auth:api', 'scope:user'])->group(function () {
    // Endpoints that require admin scope
    Route::get('/get-user', [UserController::class, 'getUser']);
    Route::patch('/update-user', [UserController::class, 'updateUser']);
   
    Route::post('/logout', [UserAuthController::class, 'logout']);


});

Route::middleware(['auth:api', 'scope:admin'])->group(function () {
    // Endpoints that require admin scope
    
   
    Route::get('/get-users', [AdminController::class, 'getUsers']);
    Route::get('/get-user/{id}', [AdminController::class, 'getUserById']);
    Route::patch('/update-user/{id}', [AdminController::class, 'changeUserInfo']);
    Route::delete('/delete-user/{id}', [AdminController::class, 'deleteUserById']);
    Route::post('/admin-logout', [AdminAuthController::class, 'Adminlogout']);
});