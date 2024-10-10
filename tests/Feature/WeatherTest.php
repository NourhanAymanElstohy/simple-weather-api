<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;
use App\Services\WeatherService;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Mockery;

class WeatherTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up the test case.
     */
    public function setUp(): void
    {
        parent::setUp();

        // Flush the cache before each test
        Cache::flush();

        // Disable the ThrottleRequests middleware for testing
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /**
     * Authenticate a user and return the JWT token.
     *
     * @return string
     */
    protected function authenticate()
    {
        // Create a new user
        $user = User::factory()->create();

        // Generate a JWT token for the user
        return JWTAuth::fromUser($user);
    }

    /**
     * Test that getting weather data requires authentication.
     */
    public function test_get_weather_data_requires_authentication()
    {
        // Send a GET request to the weather API without authentication
        $response = $this->getJson('/api/weather?city=Cairo');

        // Assert that the response status code is 401 (Unauthorized)
        $response->assertStatus(401);
    }

    /**
     * Test that an authenticated user can get weather data.
     */
    public function test_authenticated_user_can_get_weather_data()
    {
        // Authenticate the user and get the JWT token
        $token = $this->authenticate();

        // Mock the weather service response
        $mockedData = [
            'city' => 'Cairo',
            'temperature' => '30°C',
            'humidity' => '50%',
            'conditions' => 'Clear sky'
        ];
        $this->instance(WeatherService::class, Mockery::mock(
            WeatherService::class,
            function ($mock) use ($mockedData) {
                $mock->shouldReceive('getWeather')->andReturn($mockedData);
            }
        ));

        // Send a GET request to the weather API with authentication
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/weather?city=Cairo');

        // Assert that the response status code is 200 (OK) and the JSON response matches the expected data
        $response->assertStatus(200)
            ->assertJson([
                'city' => 'Cairo',
                'temperature' => '30°C',
                'humidity' => '50%',
                'conditions' => 'Clear sky'
            ]);
    }

    /**
     * Test that weather data is cached.
     */
    public function test_weather_data_is_cached()
    {
        // Authenticate the user and get the JWT token
        $token = $this->authenticate();

        // Define the city for the weather data
        $city = 'Cairo';

        // Mock the Cache::remember method to return the cached weather data
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(['city' => $city, 'temperature' => '30°C', 'humidity' => '50%', 'conditions' => 'Clear sky']);

        // Send a GET request to the weather API with authentication and the specified city
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/weather?city={$city}");

        // Assert that the response status code is 200 (OK) and the JSON response matches the expected data
        $response->assertStatus(200)
            ->assertJson([
                'city' => $city,
                'temperature' => '30°C',
                'humidity' => '50%',
                'conditions' => 'Clear sky'
            ]);
    }
}
