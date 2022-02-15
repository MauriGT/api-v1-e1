<?php

use App\Http\Controllers\Api\v1\PassportAuthController;
use App\Http\Controllers\Api\v1\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [PassportAuthController::class, 'login']);
Route::post('/register', [PassportAuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', [PassportAuthController::class, 'userInfo']);
    Route::resource('products', ProductController::class);

   /* Route::get('/product', [ProductController::class,'index']);
    Route::post('/product', [ProductController::class,'store']);
    Route::get('/product/{id}', [ProductController::class,'destroy']);*/
});

