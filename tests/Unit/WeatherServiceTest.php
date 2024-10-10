<?php

namespace Tests\Unit;

use App\Events\RetryEvent;
use Tests\TestCase;
use App\Services\WeatherService;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Mockery;

class WeatherServiceTest extends TestCase
{
    /**
     * The weather service instance.
     *
     * @var WeatherService
     */
    protected $weatherService;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Instantiate the weather service
        $this->weatherService = new WeatherService();

        // Clear the cache
        Cache::flush();

        // Disable the ThrottleRequests middleware
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /**
     * Test getting weather data for a city.
     *
     * @return void
     */
    public function test_get_weather_data()
    {
        $city = 'Cairo';
        $expectedData = [
            'temperature' => '30°C',
            'humidity' => '50%',
            'conditions' => 'Clear sky'
        ];

        // Get the weather data for the city
        $result = $this->weatherService->getWeather($city);

        // Assert that the result matches the expected data
        $this->assertEquals($expectedData, $result);
    }

    /**
     * Test that weather data is cached.
     *
     * @return void
     */
    public function test_weather_data_is_cached()
    {
        // Mock the Cache::remember method to return the cached weather data
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(['temperature' => '30°C', 'humidity' => '50%', 'conditions' => 'Clear sky']);

        $city = 'Cairo';

        // Get the weather data for the city
        $result = $this->weatherService->getWeather($city);

        // Assert that the temperature matches the expected value
        $this->assertEquals('30°C', $result['temperature']);
    }

    /**
     * Test getting weather data with retry.
     *
     * @return void
     */
    public function test_get_weather_with_retry()
    {
        // Fake the RetryEvent
        Event::fake([RetryEvent::class]);

        $city = 'Cairo';
        $expectedData = [
            'temperature' => '30°C',
            'humidity' => '50%',
            'conditions' => 'Clear sky'
        ];

        // Create a partial mock of the WeatherService class
        $weatherServiceMock = Mockery::mock(WeatherService::class)->makePartial();
        $weatherServiceMock->shouldAllowMockingProtectedMethods();

        // Mock the sleep method to avoid actual sleep
        $weatherServiceMock->shouldReceive('sleep')
            ->andReturnNull();

        // Mock the fetchWeatherData method to return the expected data
        $weatherServiceMock->shouldReceive('fetchWeatherData')
            ->once()
            ->with($city)
            ->andReturn($expectedData);

        // Get the weather data with retry
        $result = $weatherServiceMock->getWeatherWithRetry($city);

        // Assert that the result matches the expected data
        $this->assertEquals($expectedData, $result);
    }

    /**
     * Test that max retry attempts are reached.
     *
     * @return void
     */
    public function test_max_retry_attempts_reached()
    {
        $city = 'Cairo';

        // Create a partial mock of the WeatherService class
        $weatherServiceMock = Mockery::mock(WeatherService::class)->makePartial();
        $weatherServiceMock->shouldAllowMockingProtectedMethods();

        // Mock the fetchWeatherData method to simulate a failure on all retry attempts
        $weatherServiceMock->shouldReceive('fetchWeatherData')
            ->times(3)
            ->with($city)
            ->andThrow(new \Exception('Failed to fetch weather data'));

        // Expect an exception to be thrown with the appropriate message
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Failed to fetch weather data for city: $city after 3 attempts please try again later.");

        // Get the weather data for the city
        $weatherServiceMock->getWeather($city);
    }
}
