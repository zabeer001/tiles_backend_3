<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ForgetPassowrdController;
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

Route::apiResource('categories', CategoryController::class);
Route::apiResource('colors', ColorController::class);
Route::apiResource('tiles', TileController::class);
