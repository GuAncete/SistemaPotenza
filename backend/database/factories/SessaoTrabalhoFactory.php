<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Maquina;
use App\Models\Operario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SessaoTrabalho>
 */
class SessaoTrabalhoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'operario_id' => Operario::factory(),
            'maquina_id'  => Maquina::factory(),
            'inicio'      => now(),
            'fim'         => null,
        ];
    }

    public function encerrada(): static
    {
        return $this->state(fn (array $attributes) => [
            'fim' => now()->addHours(8),
        ]);
    }
}
