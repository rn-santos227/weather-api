<?php
namespace App\Domain;

use Carbon\CarbonImmutable;

final class Weather {
    public function __construct(
        public readonly string $city,
        public readonly float $temperature,
        public readonly string $description,
        public readonly CarbonImmutable $timestamp
    ) {}

    public function toArray(string $source): array {
        return [
            'city' => $this->city,
            'temperature' => $this->temperature,
            'weather_description' => $this->description,
            'timestamp' => $this->timestamp->toIso8601String(),
            'source' => $source,
        ];
    }
}
