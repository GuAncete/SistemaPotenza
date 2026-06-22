<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\EtapaFluxo;
use App\Models\Operario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_requisicao_mutante_autenticada_gera_log_automatico(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'sanctum')
            ->putJson('/api/turnos/1', [
                'hora_inicio'                    => '07:00',
                'hora_fim'                        => '16:00',
                'tolerancia_finalizacao_minutos' => 15,
                'ativo'                           => true,
            ])
            ->assertOk();

        $this->assertDatabaseHas('activity_logs', [
            'user_id'     => $admin->id,
            'method'      => 'PUT',
            'route'       => '/api/turnos/1',
            'status_code' => 200,
        ]);
    }

    public function test_requisicao_de_leitura_nao_gera_log(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/turnos')
            ->assertOk();

        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_rotas_de_apontamento_e_sessao_sao_excluidas_do_log(): void
    {
        $operario = User::factory()->operario()->create();

        $this->actingAs($operario, 'sanctum')
            ->postJson('/api/sessao/iniciar', []);

        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_login_com_senha_errada_gera_log_sem_usuario_e_com_senha_redigida(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'senha-errada',
        ])->assertStatus(401);

        $this->assertDatabaseHas('activity_logs', [
            'user_id'     => null,
            'user_name'   => 'Anônimo',
            'method'      => 'POST',
            'route'       => '/api/auth/login',
            'status_code' => 401,
        ]);

        $log = ActivityLog::first();
        $this->assertArrayNotHasKey('password', $log->payload);
    }

    public function test_login_correto_gera_log_com_usuario_associado(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertOk();

        $this->assertDatabaseHas('activity_logs', [
            'user_id'     => $user->id,
            'method'      => 'POST',
            'route'       => '/api/auth/login',
            'status_code' => 200,
        ]);
    }

    public function test_remover_operario_funciona_e_gera_log(): void
    {
        $admin = User::factory()->admin()->create();
        $etapa = EtapaFluxo::create(['nome' => 'Corte', 'ordem' => 1, 'ativa' => true]);

        $operarioUser = User::factory()->operario()->create();
        $operario = Operario::create([
            'user_id'        => $operarioUser->id,
            'matricula'      => 'OP-0001',
            'etapa_fluxo_id' => $etapa->id,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/operarios/{$operario->id}")
            ->assertOk();

        $this->assertDatabaseHas('activity_logs', [
            'user_id'     => $admin->id,
            'method'      => 'DELETE',
            'route'       => "/api/operarios/{$operario->id}",
            'status_code' => 200,
        ]);
    }
}
