<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\Gdf15StatisticsController;
use App\Http\Controllers\LifeStyleController;
use App\Http\Controllers\ReadingLogsControllr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

//
Route::middleware('auth:sanctum')->group(function () {
    //readings apis

    Route::post('/reading-logs', [ReadingLogsControllr::class, 'store']);
    Route::get('/reading-logs', [ReadingLogsControllr::class, 'readingsByDate']);
    Route::get('/reading-logs/statistics', [ReadingLogsControllr::class, 'statistics']);

    //foods apis
    Route::get('/foods-by-category', [FoodController::class, 'foodsByBategory']);
    Route::post('/add-diet', [FoodController::class, 'addFoodToDiet']);
    Route::get('/diets', [FoodController::class, 'userDiet']);
    Route::post('/foods/log', [FoodController::class, 'logFood']);

    Route::get('/foods/daily-score-by-hour', [FoodController::class, 'getDailyScoreByHour']);

    //life-styel apis
    Route::get('/life-styles', [LifeStyleController::class, 'index']);
    Route::post('/life-style-logs', [LifeStyleController::class, 'store']);
    Route::get('/life-style/daily-score-by-hour', [LifeStyleController::class, 'getDailyScoreByHour']);

    //add-charts
    Route::get('/gdf15-traking-charts', [Gdf15StatisticsController::class, 'byDate']);
});

