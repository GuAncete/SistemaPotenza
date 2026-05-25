<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Apontamento;
use App\Models\EtapaFluxo;
use App\Models\Maquina;
use App\Models\Operario;
use App\Models\SessaoTrabalho;
use App\Models\User;
use App\Services\Lote\LoteServiceInterface;
use App\Services\Lote\MockLoteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvanceLoteKanbanTest extends TestCase
{
    use RefreshDatabase;

    public function test_lote_avanca_quando_soma_atinge_qtde_total(): void
    {
        // MockLoteService retorna qtde_total = 300
        $this->app->bind(LoteServiceInterface::class, MockLoteService::class);

        $etapa    = EtapaFluxo::factory()->create(['ativa' => true]);
        $maquina  = Maquina::factory()->create(['etapa_fluxo_id' => $etapa->id, 'ativa' => true]);
        $user     = User::factory()->operario()->create();
        $operario = Operario::factory()->create(['user_id' => $user->id]);
        $sessao   = SessaoTrabalho::factory()->create([
            'operario_id' => $operario->id,
            'maquina_id'  => $maquina->id,
        ]);

        // 3 pilhas já finalizadas: 90+90+90 = 270
        foreach ([2, 3, 4] as $pilha) {
            Apontamento::factory()->finalizado(90)->create([
                'sessao_trabalho_id' => $sessao->id,
                'etapa_fluxo_id'     => $etapa->id,
                'cod_peca'           => '4501940',
                'ordem_lote'         => '06854',
                'pilha'              => $pilha,
            ]);
        }

        // Bipar pilha 1
        $bipar = $this->actingAs($user, 'sanctum')
            ->postJson('/api/apontamento/bipar', [
                'cod_peca'   => '4501940',
                'ordem_lote' => '06854',
                'qtd_peca'   => 33,
                'pilha'      => 1,
            ]);
        $bipar->assertStatus(201);
        $id = $bipar->json('data.id');

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/apontamento/{$id}/iniciar-setup")
            ->assertOk();

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/apontamento/{$id}/iniciar-producao")
            ->assertOk();

        // Finalizar com 30 → 270+30 = 300 >= 300 → concluido
        $this->actingAs($user, 'sanctum')
            ->postJson("/api/apontamento/{$id}/finalizar", ['qtd_produzida' => 30])
            ->assertOk();

        $this->assertDatabaseHas('historico_lote', [
            'etapa_fluxo_id' => $etapa->id,
            'ordem_lote'     => '06854',
            'status'         => 'concluido',
        ]);
    }
}
