<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Operario>
 */
class OperarioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'   => User::factory()->operario(),
            'matricula' => 'OP-' . fake()->unique()->numerify('#####'),
            'cargo'     => fake()->randomElement([
                'Operador de Máquina',
                'Auxiliar de Produção',
                'Técnico de Manutenção',
            ]),
        ];
    }
}
