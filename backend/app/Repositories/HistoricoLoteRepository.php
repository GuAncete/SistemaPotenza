<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\HistoricoLote;
use App\Repositories\Contracts\HistoricoLoteRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class HistoricoLoteRepository implements HistoricoLoteRepositoryInterface
{
    public function buscarOuCriar(int $etapaFluxoId, string $codPeca, string $ordemLote): HistoricoLote
    {
        return HistoricoLote::firstOrCreate(
            ['etapa_fluxo_id' => $etapaFluxoId, 'ordem_lote' => $ordemLote],
            ['cod_peca' => $codPeca, 'entrada' => Carbon::now(), 'status' => 'em_andamento']
        );
    }

    public function incrementarPilhaConcluida(HistoricoLote $historico): HistoricoLote
    {
        $historico->increment('pilhas_concluidas');

        return $historico->fresh();
    }

    public function concluir(HistoricoLote $historico): HistoricoLote
    {
        $historico->update(['status' => 'concluido', 'saida' => Carbon::now()]);

        return $historico->fresh();
    }

    public function porEtapa(int $etapaFluxoId): Collection
    {
        return HistoricoLote::where('etapa_fluxo_id', $etapaFluxoId)
            ->where('status', 'em_andamento')
            ->with('etapaFluxo')
            ->get();
    }

    public function historicoCompleto(string $ordemLote): Collection
    {
        return HistoricoLote::where('ordem_lote', $ordemLote)
            ->with('etapaFluxo')
            ->orderBy('entrada')
            ->get();
    }
}
