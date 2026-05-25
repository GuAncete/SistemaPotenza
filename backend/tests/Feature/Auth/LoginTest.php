<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        User::factory()->create([
            'email'    => 'joao@example.com',
            'password' => Hash::make('senha1234'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'joao@example.com',
            'password' => 'senha1234',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['user', 'token'],
                'errors',
            ])
            ->assertJson(['success' => true]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email'    => 'joao@example.com',
            'password' => Hash::make('senha1234'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'joao@example.com',
            'password' => 'senhaerrada',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_login_fails_with_nonexistent_email(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'naoexiste@example.com',
            'password' => 'senha1234',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_login_fails_with_missing_fields(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }
}
