<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EtapaFluxo;
use Illuminate\Database\Seeder;

class EtapaFluxoSeeder extends Seeder
{
    public function run(): void
    {
        $etapas = [
            ['nome' => 'Matéria Prima', 'ordem' => 1],
            ['nome' => 'Corte',         'ordem' => 2],
            ['nome' => 'Furadeira',     'ordem' => 3],
            ['nome' => 'Adesivagem',    'ordem' => 4],
            ['nome' => 'Pintura',       'ordem' => 5],
            ['nome' => 'Embalagem',     'ordem' => 6],
        ];

        foreach ($etapas as $etapa) {
            EtapaFluxo::firstOrCreate(
                ['ordem' => $etapa['ordem']],
                ['nome' => $etapa['nome'], 'ativa' => true]
            );
        }
    }
}
