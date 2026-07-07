<?php

namespace App\Services;

use App\Models\Country;
use App\Models\WeatherCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenMeteoService
{
    protected string $baseUrl = 'https://api.open-meteo.com/v1/forecast';

    protected array $weatherCodeMap = [
        0 => 'Clear sky',
        1 => 'Mainly clear', 2 => 'Partly cloudy', 3 => 'Overcast',
        45 => 'Fog', 48 => 'Freezing fog',
        51 => 'Light drizzle', 53 => 'Moderate drizzle', 55 => 'Dense drizzle',
        61 => 'Slight rain', 63 => 'Moderate rain', 65 => 'Heavy rain',
        71 => 'Slight snow', 73 => 'Moderate snow', 75 => 'Heavy snow',
        80 => 'Slight rain showers', 81 => 'Moderate rain showers', 82 => 'Violent rain showers',
        95 => 'Thunderstorm', 96 => 'Thunderstorm with slight hail', 99 => 'Thunderstorm with heavy hail',
    ];

    public function fetchAndCache(Country $country): ?WeatherCache
    {
        $existingCache = $country->latestWeather();
        if ($existingCache && ! $existingCache->isStale()) {
            return $existingCache;
        }

        if (! $country->latitude || ! $country->longitude) {
            Log::warning('OpenMeteoService: negara tidak punya koordinat', ['country' => $country->name]);

            return $existingCache;
        }

        try {
            $response = Http::timeout(10)->get($this->baseUrl, [
                'latitude' => $country->latitude,
                'longitude' => $country->longitude,
                'current' => 'temperature_2m,precipitation,wind_speed_10m,weather_code',
            ]);

            if (! $response->successful()) {
                return $existingCache;
            }

            $current = $response->json('current');
            if (! $current) {
                return $existingCache;
            }

            $weatherCode = $current['weather_code'] ?? 0;
            $windSpeed = $current['wind_speed_10m'] ?? 0;
            $precipitation = $current['precipitation'] ?? 0;

            return WeatherCache::create([
                'country_id' => $country->id,
                'temperature' => $current['temperature_2m'] ?? null,
                'precipitation' => $precipitation,
                'wind_speed' => $windSpeed,
                'weather_code' => $weatherCode,
                'weather_description' => $this->weatherCodeMap[$weatherCode] ?? 'Unknown',
                'storm_risk_score' => $this->calculateStormRisk($windSpeed, $precipitation),
                'fetched_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('OpenMeteoService::fetchAndCache gagal', [
                'country' => $country->name,
                'error' => $e->getMessage(),
            ]);

            return $existingCache;
        }
    }

    protected function calculateStormRisk(float $windSpeed, float $precipitation): float
    {
        $windScore = min(($windSpeed / 60) * 100, 100);
        $rainScore = min(($precipitation / 20) * 100, 100);

        $stormScore = ($windScore * 0.6) + ($rainScore * 0.4);

        return round(min($stormScore, 100), 2);
    }
}