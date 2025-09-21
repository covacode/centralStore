<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;
Use App\Http\Controllers\UserController;
Use App\Http\Controllers\StoreController;

Route::get('auth/healthCheck',[ApiController::class,'healthCheck']);
Route::post('auth/login',[ApiController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('auth/logout',[ApiController::class,'logout']);
});

Route::get('users', [UserController::class, 'index']);
Route::get('users/{user}', [UserController::class, 'show']);
Route::post('users', [UserController::class, 'store']);
Route::put('users/{user}', [UserController::class, 'update']);
Route::delete('users/{user}', [UserController::class, 'destroy']);
Route::post('users/restore/{user}', [UserController::class, 'restore']);

Route::get('stores', [StoreController::class, 'index']);
Route::get('stores/{store}', [StoreController::class, 'show']);
Route::post('stores', [StoreController::class, 'store']);
Route::put('stores/{store}', [StoreController::class, 'update']);
Route::delete('stores/{store}', [StoreController::class, 'destroy']);
