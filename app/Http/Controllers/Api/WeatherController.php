<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

        if (!$city) {
            return response()->json(['error' => 'City parameter is required'], 400);
        }

        $weatherData = $this->weatherService->getWeather($city);

        return response()->json(['city' => $city] + $weatherData);
    }
}
