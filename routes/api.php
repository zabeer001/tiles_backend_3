<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\TileController;
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


    

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');


// Routes for categories with JWT authentication
Route::middleware('auth:api')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::patch('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

// Routes for colors with JWT authentication
Route::middleware('auth:api')->group(function () {
    Route::get('/colors', [ColorController::class, 'index'])->name('colors.index');
    Route::get('/colors/{id}', [ColorController::class, 'show'])->name('colors.show');
    Route::post('/colors', [ColorController::class, 'store'])->name('colors.store');
    Route::put('/colors/{id}', [ColorController::class, 'update'])->name('colors.update');
    Route::patch('/colors/{id}', [ColorController::class, 'update'])->name('colors.update');
    Route::delete('/colors/{id}', [ColorController::class, 'destroy'])->name('colors.destroy');
});

// Routes for tiles with JWT authentication
Route::middleware('auth:api')->group(function () {
    Route::get('/tiles', [TileController::class, 'index'])->name('tiles.index');
    Route::get('/tiles/{id}', [TileController::class, 'show'])->name('tiles.show');
    Route::post('/tiles', [TileController::class, 'store'])->name('tiles.store');
    Route::put('/tiles/{id}', [TileController::class, 'update'])->name('tiles.update');
    Route::patch('/tiles/{id}', [TileController::class, 'update'])->name('tiles.update');
    Route::delete('/tiles/{id}', [TileController::class, 'destroy'])->name('tiles.destroy');
});

