<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        // Send a POST request to the '/api/register' endpoint with the registration data
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Assert that the response has a status code of 200 (OK)
        // and the JSON structure contains 'message' and 'token' keys
        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);
    }

    public function test_user_can_login()
    {
        // Create a new user using the User factory and set their password
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password')
        ]);

        // Send a POST request to the '/api/login' endpoint with the user's email and password
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        // Assert that the response has a status code of 200 (OK)
        // and the JSON structure contains a 'token' key
        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }
}
