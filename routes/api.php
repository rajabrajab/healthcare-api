<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\Gdf15StatisticsController;
use App\Http\Controllers\LifeStyleController;
use App\Http\Controllers\ReadingLogsControllr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

//
Route::middleware('auth:sanctum')->group(function () {
    //readings apis

    Route::post('/reading-logs', [ReadingLogsControllr::class, 'store']);
    Route::get('/reading-logs', [ReadingLogsControllr::class, 'readingsByDate']);
    Route::get('/reading-logs/statistics', [ReadingLogsControllr::class, 'statistics']);

    //foods apis
    Route::get('/foods/by-category', [FoodController::class, 'foodsByBategory']);
    Route::post('/foods/add-diet', [FoodController::class, 'addFoodToDiet']);
    Route::get('/foods/diets', [FoodController::class, 'userDiet']);
    Route::post('/foods/log', [FoodController::class, 'logFood']);
    Route::put('/foods/log', [FoodController::class, 'updateFoodLog']);
    Route::get('/foods/log', [FoodController::class, 'getFoodLog']);
    Route::delete('/foods/diets/{foodId}', [FoodController::class, 'deleteFromUserDiet']);
    Route::get('/foods/score', [FoodController::class, 'getScoreStats']);
    Route::post('/foods/custom-food', [FoodController::class, 'storeCustomFood']);

    //life-styel apis
    Route::get('/life-styles', [LifeStyleController::class, 'index']);
    Route::post('/life-styles/log', [LifeStyleController::class, 'store']);
    Route::get('/life-styles/log', [LifeStyleController::class, 'getLifeStyleLog']);
    Route::get('/life-styles/score', [LifeStyleController::class, 'getScoreStats']);

    //add-charts
    Route::get('/gdf15-traking-charts', [Gdf15StatisticsController::class, 'byDate']);
});

