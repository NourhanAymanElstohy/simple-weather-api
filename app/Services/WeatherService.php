<?php

namespace App\Services;

use App\Events\RetryEvent;
use App\Interfaces\WeatherServiceInterface;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherService implements WeatherServiceInterface
{
    protected int $retryCount = 3;    // Number of retry attempts
    protected array $retryIntervals = [60, 180, 300];  // Retry intervals in seconds (1 min, 3 mins, 5 mins)

    // Retrieves data from the specified source with retry mechanism.
    public function getWeather(string $city): array
    {
        // Retrieve the weather data from the cache if available,otherwise fetch it and store in the cache
        $weatherData =  Cache::remember("weather_{$city}", 3600, function () use ($city) {
            return $this->getWeatherWithRetry($city);
        });

        return $weatherData;
    }

    protected function getWeatherWithRetry(string $city, int $attempt = 0): array
    {
        try {
            // Attempt to fetch the weather data
            $weatherData = $this->fetchWeatherData($city);

            // Log cache setting if the weather data is mocked
            Log::info("Setting cache for city: $city");

            // Return the weather data if fetched successfully
            return $weatherData;
        } catch (Exception $e) {
            Log::warning("Attempt " . ($attempt + 1) . " failed for city: $city. Error: " . $e->getMessage());

            // If maximum retries are reached, throw the exception
            if ($attempt >= $this->retryCount - 1) {
                throw new Exception("Failed to fetch weather data for city: $city after {$this->retryCount} attempts please try again later.");
            }

            // Wait for the interval before retrying
            $interval = $this->retryIntervals[$attempt] ?? end($this->retryIntervals);
            event(new RetryEvent($interval));

            // Recursively call the method with the incremented attempt count
            return $this->getWeatherWithRetry($city, $attempt + 1);
        }
    }

    protected function fetchWeatherData(string $city): array
    {
        // Simulate occasional failure
        if (random_int(0, 4) === 0) {  // 20% chance of failure
            Log::warning("WeatherService failed to fetch data for city: $city");
            throw new Exception("Failed to fetch weather data for city: $city");
        }

        // Mocked data
        $mockedData = [
            'Cairo' => ['temperature' => '30°C', 'humidity' => '50%', 'conditions' => 'Clear sky'],
            'London' => ['temperature' => '15°C', 'humidity' => '70%', 'conditions' => 'Cloudy'],
            'Saudi Arabia' => ['temperature' => '40°C', 'humidity' => '30%', 'conditions' => 'Sunny'],
            'New York' => ['temperature' => '20°C', 'humidity' => '60%', 'conditions' => 'Rainy'],
        ];

        return $mockedData[$city] ?? ['city' => $city, 'temperature' => 'Unknown', 'humidity' => 'Unknown', 'conditions' => 'Unknown'];
    }
}
