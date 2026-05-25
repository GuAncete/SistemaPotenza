<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EtapaFluxo;
use App\Models\SessaoTrabalho;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apontamento>
 */
class ApontamentoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sessao_trabalho_id' => SessaoTrabalho::factory(),
            'etapa_fluxo_id'     => EtapaFluxo::factory(),
            'cod_peca'           => fake()->numerify('#######'),
            'ordem_lote'         => fake()->numerify('#####'),
            'qtd_peca'           => fake()->numberBetween(1, 200),
            'pilha'              => 1,
            'desc_peca'          => fake()->words(3, true),
            'cod_produto'        => 'PROD-' . fake()->numerify('####'),
            'qtd_produzida'      => null,
            'status'             => 'em_setup',
        ];
    }

    public function emProducao(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'em_producao']);
    }

    public function finalizado(int $qtdProduzida = 33): static
    {
        return $this->state(fn (array $attributes) => [
            'status'        => 'finalizado',
            'qtd_produzida' => $qtdProduzida,
        ]);
    }
}
