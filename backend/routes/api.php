<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Weather API routes
Route::prefix('weather')->group(function () {
    Route::get('/', [WeatherController::class, 'getWeather']);
});
