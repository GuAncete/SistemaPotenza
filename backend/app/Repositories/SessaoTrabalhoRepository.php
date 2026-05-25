<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Operario;
use App\Models\SessaoTrabalho;
use App\Repositories\Contracts\SessaoTrabalhoRepositoryInterface;
use Carbon\Carbon;

class SessaoTrabalhoRepository implements SessaoTrabalhoRepositoryInterface
{
    public function criarSessao(int $operarioId, int $maquinaId): SessaoTrabalho
    {
        return SessaoTrabalho::create([
            'operario_id' => $operarioId,
            'maquina_id'  => $maquinaId,
            'inicio'      => Carbon::now(),
        ]);
    }

    public function encerrarSessao(SessaoTrabalho $sessao): SessaoTrabalho
    {
        $sessao->update(['fim' => Carbon::now()]);

        return $sessao->fresh();
    }

    public function buscarSessaoAtiva(Operario $operario): ?SessaoTrabalho
    {
        return SessaoTrabalho::where('operario_id', $operario->id)
            ->whereNull('fim')
            ->with(['maquina.etapaFluxo'])
            ->first();
    }

    public function encerrarSessoesAtivas(Operario $operario): void
    {
        SessaoTrabalho::where('operario_id', $operario->id)
            ->whereNull('fim')
            ->update(['fim' => Carbon::now()]);
    }
}
