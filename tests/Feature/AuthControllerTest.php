<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'johnsmith@examplemail.com',
            'password' => '123456',
            'password_confirmation' => '123456',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertOk()
            ->assertJson([
                'message' => 'User registered successfully',
            ]);
    }

    public function test_user_can_login()
    {
        User::factory()->create([
            'email' => 'johnsmith@examplemail.com',
            'password' => bcrypt('123456'),
        ]);

        $loginData = [
            'email' => 'johnsmith@examplemail.com',
            'password' => '123456',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'token_type',
                'expires_in',
            ]);
    }

    public function test_login_with_invalid_credentials()
    {
        $userData = [
            'email' => 'johnsmith@examplemail.com',
            'password' => 'anincorrectpass',
        ];

        $response = $this->postJson('/api/login', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertOk()
            ->assertJson([
                'message' => 'Logged out',
            ]);
    }

    public function test_authenticated_user_can_access_profile()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/profile');

        $response->assertOk()
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function test_guest_cannot_access_profile()
    {
        $response = $this->getJson('/api/profile');

        $response->assertStatus(401);
    }
}
