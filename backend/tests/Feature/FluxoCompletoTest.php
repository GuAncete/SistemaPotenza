<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EtapaFluxo;
use App\Models\Maquina;
use App\Models\Operario;
use App\Models\SessaoTrabalho;
use App\Models\User;
use App\Services\Lote\LoteServiceInterface;
use App\Services\Lote\MockLoteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FluxoCompletoTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_bipar_setup_producao_finalizar(): void
    {
        $this->app->bind(LoteServiceInterface::class, MockLoteService::class);

        $etapa    = EtapaFluxo::factory()->create(['ativa' => true]);
        $maquina  = Maquina::factory()->create(['etapa_fluxo_id' => $etapa->id, 'ativa' => true]);
        $user     = User::factory()->operario()->create();
        $operario = Operario::factory()->create(['user_id' => $user->id]);

        SessaoTrabalho::factory()->create([
            'operario_id' => $operario->id,
            'maquina_id'  => $maquina->id,
        ]);

        $bipar = $this->actingAs($user, 'sanctum')
            ->postJson('/api/apontamento/bipar', [
                'cod_peca'   => '4501940',
                'ordem_lote' => '06854',
                'qtd_peca'   => 33,
                'pilha'      => 1,
            ]);

        $bipar->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'em_setup');

        $id = $bipar->json('data.id');
        $this->assertNotNull($id);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/apontamento/{$id}/iniciar-setup")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/apontamento/{$id}/iniciar-producao")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/apontamento/{$id}/finalizar", ['qtd_produzida' => 30])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'finalizado')
            ->assertJsonPath('data.qtd_produzida', 30);

        $this->assertDatabaseHas('apontamentos', [
            'id'            => $id,
            'status'        => 'finalizado',
            'qtd_produzida' => 30,
        ]);

        $this->assertDatabaseCount('etapas_producao', 2);
    }
}
