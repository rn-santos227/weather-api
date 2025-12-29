<?php

use App\Exceptions\CityNotFoundException;
use App\Exceptions\WeatherProviderUnavailableException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(
            fn (CityNotFoundException $e) => response()->json([
                'message' => 'City not found',
                'code' => 'WEATHER_CITY_NOT_FOUND',
            ], 404)
        );

        $exceptions->render(
            fn (WeatherProviderUnavailableException $e) => response()->json([
                'message' => 'Weather service unavailable',
                'code' => 'WEATHER_PROVIDER_UNAVAILABLE',
            ], 503)
        );

        $exceptions->render(
            fn (ValidationException $e) => response()->json([
                'message' => 'Invalid request',
                'errors' => $e->errors(),
                'code' => 'VALIDATION_ERROR',
            ], 422)
        );
    })->create();
