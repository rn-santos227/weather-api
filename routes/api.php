<?php

use App\Http\Controllers\WeatherController;

use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
]));

Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/weather/{city}', [WeatherController::class, 'live']);
    Route::get('/weather/{city}/cached', [WeatherController::class, 'cached']);
});
