<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\ApiResponse;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
Use App\Http\Controllers\UserController;
Use App\Http\Controllers\StoreController;

Route::get('auth/healthCheck',[ApiController::class,'healthCheck']);

Route::get('login', function () {
    return ApiResponse::unauthorized();
})->name('login');

Route::post('auth/login',[ApiController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('auth/logout',[ApiController::class,'logout']);

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
    Route::post('stores/restore/{store}', [StoreController::class, 'restore']);

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);
    Route::post('products/restore/{product}', [ProductController::class, 'restore']);

    Route::get('stocks', [StockController::class, 'index']);
    Route::post('stocks/byStore/{store}', [StockController::class, 'byStore']);
    Route::post('stocks/byProduct/{product}', [StockController::class, 'byProduct']);
    Route::post('stocks/reserve', [StockController::class, 'reserve']);
    Route::post('stocks/release', [StockController::class, 'release']);
    Route::post('stocks/sell', [StockController::class, 'sell']);
});
