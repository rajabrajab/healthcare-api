<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\ReadingLogsControllr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::post('/reading-logs', [ReadingLogsControllr::class, 'store']);

Route::get('/reading-logs', [ReadingLogsControllr::class, 'readingsByDate']);

Route::get('/reading-logs/statistics', [ReadingLogsControllr::class, 'statistics']);


//
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/foods-by-category', [FoodController::class, 'foodsByBategory']);
    Route::post('/add-diet', [FoodController::class, 'addFoodToDiet']);
    Route::get('/diets', [FoodController::class, 'userDiet']);
});

