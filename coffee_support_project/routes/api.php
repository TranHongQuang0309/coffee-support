<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CoffeeFarmController;
use App\Http\Controllers\Api\CultivationLogController;
use App\Http\Controllers\Api\TechnicalCategoryController;
use App\Http\Controllers\Api\TechnicalArticleController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('coffee-farms', CoffeeFarmController::class);
    Route::apiResource('cultivation-logs', CultivationLogController::class);
    Route::apiResource('technical-categories', TechnicalCategoryController::class);
    Route::apiResource('technical-articles', TechnicalArticleController::class);
});