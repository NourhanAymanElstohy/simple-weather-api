<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Cache;

class WeatherServiceTest extends TestCase
{
    protected $weatherService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->weatherService = new WeatherService();
    }

    public function test_get_weather_data()
    {
        $city = 'Cairo';
        $expectedData = [
            'temperature' => '30°C',
            'humidity' => '50%',
            'conditions' => 'Clear sky'
        ];

        $result = $this->weatherService->getWeather($city);
        $this->assertEquals($expectedData, $result);
    }

    public function test_weather_data_is_cached()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(['temperature' => '30°C', 'humidity' => '50%', 'conditions' => 'Clear sky']);

        $city = 'Cairo';
        $result = $this->weatherService->getWeather($city);

        $this->assertEquals('30°C', $result['temperature']);
    }
}
