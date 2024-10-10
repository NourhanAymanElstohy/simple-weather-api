<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WeatherController;
use Illuminate\Support\Facades\Route;

// Define routes and make rate limiting using the throttle middleware
Route::middleware(['throttle:60,1'])->group(function () {
    // Register route
    Route::post('/register', [AuthController::class, 'register']);

    // Login route
    Route::post('/login', [AuthController::class, 'login']);

    // Routes that require authentication
    Route::middleware(['auth:api'])->group(function () {
        // Get weather route
        Route::get('/weather', [WeatherController::class, 'getWeather']);

        // Logout route
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

