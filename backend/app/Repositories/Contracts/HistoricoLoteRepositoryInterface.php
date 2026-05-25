<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\HistoricoLote;
use Illuminate\Database\Eloquent\Collection;

interface HistoricoLoteRepositoryInterface
{
    public function buscarOuCriar(int $etapaFluxoId, string $codPeca, string $ordemLote): HistoricoLote;

    public function incrementarPilhaConcluida(HistoricoLote $historico): HistoricoLote;

    public function concluir(HistoricoLote $historico): HistoricoLote;

    public function porEtapa(int $etapaFluxoId): Collection;

    public function historicoCompleto(string $ordemLote): Collection;
}
