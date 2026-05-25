<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ControleAcessoRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_gestor_pode_acessar_kanban(): void
    {
        $gestor = User::factory()->gestor()->create();

        $this->actingAs($gestor, 'sanctum')
            ->getJson('/api/kanban')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_admin_pode_acessar_kanban(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/kanban')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_operario_nao_pode_acessar_kanban(): void
    {
        $user = User::factory()->operario()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/kanban')
            ->assertForbidden();
    }

    public function test_gestor_nao_pode_bipar_ficha(): void
    {
        $gestor = User::factory()->gestor()->create();

        $this->actingAs($gestor, 'sanctum')
            ->postJson('/api/apontamento/bipar', [
                'cod_peca'   => '4501940',
                'ordem_lote' => '06854',
                'qtd_peca'   => 33,
                'pilha'      => 1,
            ])
            ->assertForbidden();
    }

    public function test_admin_pode_listar_maquinas(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/maquinas')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_operario_nao_pode_gerenciar_maquinas(): void
    {
        $user = User::factory()->operario()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/maquinas')
            ->assertForbidden();
    }

    public function test_gestor_nao_pode_gerenciar_maquinas(): void
    {
        $gestor = User::factory()->gestor()->create();

        $this->actingAs($gestor, 'sanctum')
            ->getJson('/api/maquinas')
            ->assertForbidden();
    }

    public function test_unauthenticated_nao_pode_acessar_rotas_protegidas(): void
    {
        $this->getJson('/api/kanban')->assertUnauthorized();
        $this->getJson('/api/maquinas')->assertUnauthorized();
        $this->getJson('/api/sessao/ativa')->assertUnauthorized();
    }
}
