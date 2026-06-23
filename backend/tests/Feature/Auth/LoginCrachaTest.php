<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\Operario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginCrachaTest extends TestCase
{
    use RefreshDatabase;

    public function test_operario_can_login_with_valid_matricula(): void
    {
        $user = User::factory()->operario()->create();
        Operario::factory()->create([
            'user_id'   => $user->id,
            'matricula' => 'OP-0001',
        ]);

        $response = $this->postJson('/api/auth/login-cracha', [
            'matricula' => 'OP-0001',
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

    public function test_login_cracha_fails_with_unknown_matricula(): void
    {
        $response = $this->postJson('/api/auth/login-cracha', [
            'matricula' => 'OP-9999',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_login_cracha_fails_for_inactive_operario(): void
    {
        $user = User::factory()->operario()->create(['ativo' => false]);
        Operario::factory()->create([
            'user_id'   => $user->id,
            'matricula' => 'OP-0002',
        ]);

        $response = $this->postJson('/api/auth/login-cracha', [
            'matricula' => 'OP-0002',
        ]);

        $response->assertStatus(403)
            ->assertJson(['success' => false]);
    }

    public function test_login_cracha_fails_with_missing_matricula(): void
    {
        $response = $this->postJson('/api/auth/login-cracha', []);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }
}
