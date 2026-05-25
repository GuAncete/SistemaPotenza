<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Operario;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@potenza.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'gestor@potenza.com'],
            [
                'name'     => 'Gestor Produção',
                'password' => Hash::make('password'),
                'role'     => 'gestor',
            ]
        );

        $operario1 = User::firstOrCreate(
            ['email' => 'operario1@potenza.com'],
            [
                'name'     => 'João da Silva',
                'password' => Hash::make('password'),
                'role'     => 'operario',
            ]
        );

        $operario2 = User::firstOrCreate(
            ['email' => 'operario2@potenza.com'],
            [
                'name'     => 'Maria Souza',
                'password' => Hash::make('password'),
                'role'     => 'operario',
            ]
        );

        Operario::firstOrCreate(
            ['user_id' => $operario1->id],
            ['matricula' => 'OP001', 'cargo' => 'Operador de Máquina']
        );

        Operario::firstOrCreate(
            ['user_id' => $operario2->id],
            ['matricula' => 'OP002', 'cargo' => 'Operador de Máquina']
        );
    }
}
