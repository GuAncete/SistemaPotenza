<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'João Silva',
            'email'                 => 'joao@example.com',
            'password'              => 'senha1234',
            'password_confirmation' => 'senha1234',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'created_at'],
                    'token',
                ],
                'errors',
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', ['email' => 'joao@example.com']);
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'joao@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'João Silva',
            'email'                 => 'joao@example.com',
            'password'              => 'senha1234',
            'password_confirmation' => 'senha1234',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_register_fails_with_missing_fields(): void
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['errors']);
    }

    public function test_register_fails_when_passwords_do_not_match(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'João Silva',
            'email'                 => 'joao@example.com',
            'password'              => 'senha1234',
            'password_confirmation' => 'outrasenha',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}
