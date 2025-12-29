<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

use App\Services\OpenWeatherClient;
use App\Domain\Weather;
use Carbon\CarbonImmutable;

class WeatherRequestTest extends TestCase
{
    private function mockWeatherClient(string $city = 'New York'): void {
        $this->mock(OpenWeatherClient::class, function ($mock) use ($city) {
            $mock->shouldReceive('fetchByCity')
                ->once()
                ->with($city)
                ->andReturn(
                    new Weather(
                        city: $city,
                        temperature: 25,
                        description: 'Clear',
                        timestamp: CarbonImmutable::now()
                    )
                );
        });
    }

    #[Test]
    public function it_accepts_a_city_with_spaces() {
        $this->mockWeatherClient('New York');

        $response = $this->getJson('/api/weather/New%20York');

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'city' => 'New York',
            ]);
    }

    #[Test]
    public function it_accepts_a_hyphenated_city() {
        $this->mockWeatherClient('New York');

        $response = $this->getJson('/api/weather/new-york');

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'city' => 'New York',
            ]);
    }

    #[Test]
    public function it_rejects_city_with_numbers() {
        $response = $this->getJson('/api/weather/New-York123');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('city');
    }

    #[Test]
    public function it_rejects_city_with_special_characters() {
        $response = $this->getJson('/api/weather/New@York');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('city');
    }

    #[Test]
    public function it_rejects_missing_city() {
        $response = $this->getJson('/api/weather/');

        $response->assertStatus(404);
    }

    #[Test]
    public function it_trims_and_normalizes_city_name() {
        $this->mockWeatherClient('New York');

        $response = $this->getJson('/api/weather/%20%20new---york%20%20');

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'city' => 'New York',
            ]);
    }
}
