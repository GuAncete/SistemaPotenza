<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EtapaFluxo>
 */
class EtapaFluxoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome'  => fake()->unique()->word(),
            'ordem' => fake()->unique()->numberBetween(1, 99),
            'ativa' => true,
        ];
    }
}
