<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', 'logout');
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});

// Public routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{product_id}', [CartController::class, 'update']);
    Route::delete('/cart/{product_id}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);
});

Route::middleware('auth:sanctum')->post('/order', [OrderController::class, 'store']);
Route::middleware('auth:sanctum')->get('/order', [OrderController::class, 'index']);
Route::middleware('auth:sanctum')->patch('/order/{id}', [OrderController::class, 'updateStatus']);
Route::middleware('auth:sanctum')->get('/order/{id}', [OrderController::class, 'show']);
Route::delete('/order', [OrderController::class, 'destroyAll']);

Route::post('/chat', [\App\Http\Controllers\ChatController::class, 'chat']);