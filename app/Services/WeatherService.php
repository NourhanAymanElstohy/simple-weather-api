<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

interface WeatherServiceInterface
{
    public function getWeather($city);
}


class WeatherService implements WeatherServiceInterface
{
    public function getWeather($city)
    {
        $cacheKey = "weather_{$city}";

        $weatherData =  Cache::remember($cacheKey, 3600, function () use ($city) {

            $mockData = [
                'Cairo' => ['temperature' => '30°C', 'humidity' => '50%', 'conditions' => 'Clear sky'],
                'London' => ['temperature' => '15°C', 'humidity' => '70%', 'conditions' => 'Cloudy'],
                'Saudi Arabia' => ['temperature' => '40°C', 'humidity' => '30%', 'conditions' => 'Sunny'],
                'New York' => ['temperature' => '20°C', 'humidity' => '60%', 'conditions' => 'Rainy'],
            ];

            if (isset($mockData[$city]))
                Log::info("Setting cache for weather_{$city}");

            return $mockData[$city] ?? ['temperature' => 'Unknown', 'humidity' => 'Unknown', 'conditions' => 'Unknown'];
        });

        return $weatherData;
    }
}
