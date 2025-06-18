<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;

Route::get('/debug-test', function () {
    return 'api.php is working!';
});

Route::middleware(['auth:sanctum'])->get('/users', [UserController::class, 'index']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/products', [ProductController::class, 'index']); // list all
Route::get('/products/{product}', [ProductController::class, 'show']); // view one

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products', [ProductController::class, 'store']); // add new
    Route::put('/products/{product}', [ProductController::class, 'update']); // update
    Route::delete('/products/{product}', [ProductController::class, 'destroy']); // delete
});


Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']); // place order
    Route::apiResource('categories', CategoryController::class);
    Route::get('/orders', [OrderController::class, 'index']);  // view user’s orders
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categories', CategoryController::class)->only(['index', 'store']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/user/promote/{id}', [UserController::class, 'promoteToAdmin']);
    Route::post('/user/demote/{id}', [UserController::class, 'demoteToUser']);
    Route::post('/user/ban/{id}', [UserController::class, 'banUser']);
    Route::post('/admin/create', [UserController::class, 'createAdmin']);
});




