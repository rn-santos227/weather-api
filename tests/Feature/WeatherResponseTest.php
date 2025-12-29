<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

use App\Services\OpenWeatherClient;
use App\Domain\Weather;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;

class WeatherResponseTest extends TestCase
{
    protected function setUp(): void {
        parent::setUp();
        Cache::flush();
    }

    private function mockWeather(string $city, string $source = 'external'): void {
        $this->mock(OpenWeatherClient::class, function ($mock) use ($city) {
            $mock->shouldReceive('fetchByCity')
                ->once()
                ->with($city)
                ->andReturn(
                    new Weather(
                        city: $city,
                        temperature: 25.5,
                        description: 'Clear sky',
                        timestamp: CarbonImmutable::parse('2025-01-01T10:00:00Z')
                    )
                );
        });
    }

    #[Test]
    public function it_returns_correct_live_weather_response() {
        $this->mockWeather('New York');

        $response = $this->getJson('/api/weather/new-york');

        $response
            ->assertStatus(200)
            ->assertJson([
                'city' => 'New York',
                'temperature' => 25.5,
                'weather_description' => 'Clear sky',
                'source' => 'external',
            ])
            ->assertJsonStructure([
                'city',
                'temperature',
                'weather_description',
                'timestamp',
                'source',
            ]);
    }


    #[Test]
    public function it_returns_cached_weather_on_second_request() {
        Cache::put(
            'weather:new york',
            new Weather(
                city: 'New York',
                temperature: 25.5,
                description: 'Clear sky',
                timestamp: CarbonImmutable::parse('2025-01-01T10:00:00Z')
            ),
            600
        );

        $response = $this->getJson('/api/weather/new-york/cached');
        $response
            ->assertStatus(200)
            ->assertJson([
                'city' => 'New York',
                'source' => 'cache',
            ]);
    }
}
