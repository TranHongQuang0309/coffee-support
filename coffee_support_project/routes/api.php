<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CoffeeFarmController;
use App\Http\Controllers\Api\CultivationLogController;
use App\Http\Controllers\Api\TechnicalCategoryController;
use App\Http\Controllers\Api\TechnicalArticleController;
use App\Http\Controllers\Api\DiseaseController;
use App\Http\Controllers\Api\DiagnosisRequestController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\MarketPriceController;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Api\Admin\DashboardController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
   

    Route::apiResource('coffee-farms', CoffeeFarmController::class);
    Route::apiResource('cultivation-logs', CultivationLogController::class);
    Route::apiResource('technical-categories', TechnicalCategoryController::class);
    Route::apiResource('technical-articles', TechnicalArticleController::class);
    Route::apiResource('diseases', DiseaseController::class);
    Route::apiResource('diagnosis-requests', DiagnosisRequestController::class);
    Route::apiResource('questions', QuestionController::class);
    Route::apiResource('answers', AnswerController::class);
    Route::apiResource('market-prices', MarketPriceController::class);
    
    Route::get('/weather/farms/{farm}', [WeatherController::class, 'getByFarm']);

    Route::get('/admin/dashboard/statistics', [DashboardController::class, 'statistics']);

});