<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EtapaFluxo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Maquina>
 */
class MaquinaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'etapa_fluxo_id' => EtapaFluxo::factory(),
            'nome'           => fake()->randomElement(['Furadeira', 'Serra', 'Cabine', 'Bancada'])
                                . ' ' . fake()->numerify('##'),
            'descricao'      => null,
            'ativa'          => true,
        ];
    }
}
