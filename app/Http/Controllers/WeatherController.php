<?php

namespace App\Http\Controllers;

use App\Http\Requests\WeatherRequest;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    public function __construct(
        private readonly WeatherService $weatherService
    ) {}

    public function live(WeatherRequest $request, string $city): JsonResponse {
        return response()->json(
            $this->weatherService
                ->getLiveWeather($city)
                ->toArray('external')
        );
    }

    public function cached(WeatherRequest $request, string $city): JsonResponse {
        $result = $this->weatherService->getCachedWeather($city);

        return response()->json(
            $result['weather']->toArray(
                $result['from_cache'] ? 'cache' : 'external'
            )
        );
    }
}
