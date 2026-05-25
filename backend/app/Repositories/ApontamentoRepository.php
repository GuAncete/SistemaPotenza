<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Apontamento;
use App\Models\SessaoTrabalho;
use App\Repositories\Contracts\ApontamentoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ApontamentoRepository implements ApontamentoRepositoryInterface
{
    public function criar(array $dados): Apontamento
    {
        return Apontamento::create($dados);
    }

    public function buscarPorId(int $id): ?Apontamento
    {
        return Apontamento::with(['etapasProducao', 'sessaoTrabalho.maquina.etapaFluxo'])->find($id);
    }

    public function buscarApontamentoAtivo(SessaoTrabalho $sessao): ?Apontamento
    {
        return Apontamento::where('sessao_trabalho_id', $sessao->id)
            ->whereIn('status', ['em_setup', 'em_producao'])
            ->first();
    }

    public function pilhaJaBipada(int $etapaFluxoId, string $codPeca, string $ordemLote, int $pilha): bool
    {
        return Apontamento::where('etapa_fluxo_id', $etapaFluxoId)
            ->where('cod_peca', $codPeca)
            ->where('ordem_lote', $ordemLote)
            ->where('pilha', $pilha)
            ->exists();
    }

    public function somarQtdProduzida(int $etapaFluxoId, string $ordemLote): int
    {
        return (int) Apontamento::where('etapa_fluxo_id', $etapaFluxoId)
            ->where('ordem_lote', $ordemLote)
            ->where('status', 'finalizado')
            ->sum('qtd_produzida');
    }

    public function historicoPorOperario(int $operarioId): Collection
    {
        return Apontamento::whereHas('sessaoTrabalho', fn ($q) => $q->where('operario_id', $operarioId))
            ->with(['etapasProducao', 'etapaFluxo'])
            ->latest()
            ->get();
    }
}
