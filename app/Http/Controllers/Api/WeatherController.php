<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function getWeather(Request $request)
    {
        $city = $request->query('city');

        // Check if city parameter is provided
        if (!$city) {
            return response()->json(['error' => 'City parameter is required'], 400);
        }

        try {
            // Fetch weather data from the weather service
            $weatherData = $this->weatherService->getWeather($city);

            // Return the weather data along with the city name
            return response()->json(['city' => $city] + $weatherData, 200);
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
