<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EtapaFluxo;
use App\Models\Maquina;
use Illuminate\Database\Seeder;

class MaquinaSeeder extends Seeder
{
    public function run(): void
    {
        $maquinas = [
            'Matéria Prima' => ['Mesa de Recebimento 01', 'Mesa de Recebimento 02'],
            'Corte'         => ['Serra Fita 01',           'Serra Fita 02'],
            'Furadeira'     => ['Furadeira 01',            'Furadeira 02'],
            'Adesivagem'    => ['Bancada Adesivagem 01',   'Bancada Adesivagem 02'],
            'Pintura'       => ['Cabine de Pintura 01',    'Cabine de Pintura 02'],
            'Embalagem'     => ['Bancada Embalagem 01',    'Bancada Embalagem 02'],
        ];

        foreach ($maquinas as $etapaNome => $nomes) {
            $etapa = EtapaFluxo::where('nome', $etapaNome)->first();

            if (! $etapa) {
                continue;
            }

            foreach ($nomes as $nome) {
                Maquina::firstOrCreate(
                    ['nome' => $nome, 'etapa_fluxo_id' => $etapa->id],
                    ['ativa' => true]
                );
            }
        }
    }
}
