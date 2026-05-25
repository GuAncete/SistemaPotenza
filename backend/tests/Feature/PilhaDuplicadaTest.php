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

class PilhaDuplicadaTest extends TestCase
{
    use RefreshDatabase;

    public function test_nao_pode_bipar_mesma_pilha_na_mesma_etapa(): void
    {
        $this->app->bind(LoteServiceInterface::class, MockLoteService::class);

        $etapa    = EtapaFluxo::factory()->create(['ativa' => true]);
        $maquina  = Maquina::factory()->create(['etapa_fluxo_id' => $etapa->id, 'ativa' => true]);
        $user     = User::factory()->operario()->create();
        $operario = Operario::factory()->create(['user_id' => $user->id]);
        $sessao   = SessaoTrabalho::factory()->create([
            'operario_id' => $operario->id,
            'maquina_id'  => $maquina->id,
        ]);

        Apontamento::factory()->finalizado(33)->create([
            'sessao_trabalho_id' => $sessao->id,
            'etapa_fluxo_id'     => $etapa->id,
            'cod_peca'           => '4501940',
            'ordem_lote'         => '06854',
            'pilha'              => 1,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/apontamento/bipar', [
                'cod_peca'   => '4501940',
                'ordem_lote' => '06854',
                'qtd_peca'   => 33,
                'pilha'      => 1,
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_pilhas_diferentes_do_mesmo_lote_podem_ser_bipadas(): void
    {
        $this->app->bind(LoteServiceInterface::class, MockLoteService::class);

        $etapa    = EtapaFluxo::factory()->create(['ativa' => true]);
        $maquina  = Maquina::factory()->create(['etapa_fluxo_id' => $etapa->id, 'ativa' => true]);
        $user     = User::factory()->operario()->create();
        $operario = Operario::factory()->create(['user_id' => $user->id]);
        $sessao   = SessaoTrabalho::factory()->create([
            'operario_id' => $operario->id,
            'maquina_id'  => $maquina->id,
        ]);

        Apontamento::factory()->finalizado(33)->create([
            'sessao_trabalho_id' => $sessao->id,
            'etapa_fluxo_id'     => $etapa->id,
            'cod_peca'           => '4501940',
            'ordem_lote'         => '06854',
            'pilha'              => 2,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/apontamento/bipar', [
                'cod_peca'   => '4501940',
                'ordem_lote' => '06854',
                'qtd_peca'   => 33,
                'pilha'      => 1,
            ])
            ->assertStatus(201)
            ->assertJsonPath('success', true);
    }
}
