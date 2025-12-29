<?php

namespace App\Services;

use App\Domain\Weather;
use App\Exceptions\CityNotFoundException;
use App\Exceptions\WeatherProviderUnavailableException;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class OpenWeatherClient {
    public function fetchByCity(string $city): Weather {
        try {
            $response = Http::timeout(config('app.openweather.timeout'))
                ->retry(2, 200)
                ->get(
                    config('app.openweather.base_url') . '/weather',
                    [
                        'q' => $city,
                        'appid' => config('app.openweather.key'),
                        'units' => config('app.openweather.units'),
                    ]
                );
        } catch (RequestException $e) {
            throw new WeatherProviderUnavailableException(
                'Weather provider is unreachable',
                previous: $e
            );
        }

        if ($response->status() === 404) {
            throw new CityNotFoundException("City '{$city}' not found");
        }

        if (! $response->successful()) {
            throw new WeatherProviderUnavailableException(
                'Weather provider returned an unexpected response'
            );
        }
        return $this->mapToWeather($response->json(), $city);
    }

    private function mapToWeather(array $data, string $city): Weather {
        return new Weather(
            city: $city,
            temperature: (float) $data['main']['temp'],
            description: (string) $data['weather'][0]['description'],
            timestamp: CarbonImmutable::now()
        );
    }
}
