<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;
use App\Services\WeatherService;
use Mockery;

class WeatherTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        return JWTAuth::fromUser($user);
    }

    public function test_get_weather_data_requires_authentication()
    {
        $response = $this->getJson('/api/weather?city=Cairo');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_get_weather_data()
    {
        $token = $this->authenticate();

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

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/weather?city=Cairo');

        $response->assertStatus(200)
            ->assertJson([
                'city' => 'Cairo',
                'temperature' => '30°C',
                'humidity' => '50%',
                'conditions' => 'Clear sky'
            ]);
    }

    public function test_weather_data_is_cached()
    {
        $token = $this->authenticate();
        $city = 'Cairo';

        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(['city' => $city, 'temperature' => '30°C', 'humidity' => '50%', 'conditions' => 'Clear sky']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/weather?city={$city}");

        $response->assertStatus(200)
            ->assertJson([
                'city' => $city,
                'temperature' => '30°C',
                'humidity' => '50%',
                'conditions' => 'Clear sky'
            ]);
    }
}
