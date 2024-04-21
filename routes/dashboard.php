<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ApartmentController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\RentController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);


});
Route::group([
    'middleware' => 'api',
    'prefix' => 'dashboard'
], function ($router) {
    //users
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{user}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::post('/users/{user}', [UserController::class, 'update']);
Route::delete('/users/{user}', [UserController::class, 'destroy']);
Route::get('getUserCount', [UserController::class, 'getUserCount']);
//roles
Route::get('/roles', [RoleController::class, 'index']);
Route::get('/roles/{role}', [RoleController::class, 'show']);
Route::post('/roles', [RoleController::class, 'store']);
Route::post('/roles/{role}', [RoleController::class, 'update']);
Route::delete('/roles/{role}', [RoleController::class, 'destroy']);

//setting
Route::get('/setting', [SettingController::class, 'index']);
Route::post('/setting', [SettingController::class, 'store']);

//Apartment
Route::get('apartments', [ApartmentController::class, 'index']);
Route::post('apartments', [ApartmentController::class, 'store']);
Route::get('apartments/{id}', [ApartmentController::class, 'show']);
Route::post('apartments/{id}', [ApartmentController::class, 'update']);
Route::delete('apartments/{id}', [ApartmentController::class, 'destroy']);

//expenses
Route::get('expenses', [ExpenseController::class, 'index']);
Route::post('expenses', [ExpenseController::class, 'store']);
Route::get('expenses/{id}', [ExpenseController::class, 'show']);
Route::post('expenses/{id}', [ExpenseController::class, 'update']);
Route::delete('expenses/{id}', [ExpenseController::class, 'destroy']);

//rents
Route::get('rents', [RentContro1ller::class, 'index']);
Route::post('rents', [RentController::class, 'store']);
Route::get('rents/{id}', [RentController::class, 'show']);
Route::post('rents/{id}', [RentController::class, 'update']);
Route::delete('rents/{id}', [RentController::class, 'destroy']);

});





