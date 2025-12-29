<?php

namespace App\Services;

use App\Domain\Weather;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;

class WeatherService {
    private const CACHE_TTL_SECONDS = 600;

    public function getLiveWeather(string $city): Weather {
        return new Weather(
            $city,
            0.0,
            'unknown',
            CarbonImmutable::now()
        );
    }

    public function getCachedWeather(string $city): array {
        $cacheKey = $this->cacheKey($city);

        $cached = Cache::get($cacheKey);
        if ($cached instanceof Weather) {
            return [
                'weather' => $cached,
                'from_cache' => true,
            ];
        }

        $weather = $this->getLiveWeather($city);
        Cache::put($cacheKey, $weather, self::CACHE_TTL_SECONDS);
        return [
            'weather' => $weather,
            'from_cache' => false,
        ];
    }

    private function cacheKey(string $city): string {
        return 'weather:' . mb_strtolower(trim($city));
    }
}
