<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ForgetPassowrdController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TileController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::post('send-reset-email-link', [ForgetPassowrdController::class, 'sendResetEmailLink']);
Route::post('reset-password', [ForgetPassowrdController::class, 'resetPassword']);

// Protected routes

//categories
Route::apiResource('categories', CategoryController::class);
Route::put('categories/status/{id}', [CategoryController::class, 'statusUpdate']);


//colors
Route::apiResource('colors', ColorController::class);
Route::put('colors/status/{id}', [ColorController::class, 'statusUpdate']);

// tiles
Route::apiResource('tiles', TileController::class);
Route::put('tiles/status/{id}', [TileController::class, 'statusUpdate']);


//orders
Route::apiResource('orders', OrderController::class);
Route::put('orders/status/{id}', [TileController::class, 'statusUpdate']);
