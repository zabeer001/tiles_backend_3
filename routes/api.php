<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ForgetPassowrdController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\TileController;
use App\Http\Controllers\TilesEmailController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');

/* create by abu sayed (start)*/ 

Route::post('password/email', [AuthController::class, 'sendResetEmailLink']);
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');



/* create by abu sayed (end)*/ 

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

Route::post('tile-select/{id}', [TileController::class, 'tileSelect']);

Route::post('/send-cloud-mail', [TilesEmailController::class, 'sendMailWithCloudFile']);
