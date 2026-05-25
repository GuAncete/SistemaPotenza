<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Apontamento;
use App\Models\SessaoTrabalho;
use Illuminate\Database\Eloquent\Collection;

interface ApontamentoRepositoryInterface
{
    public function criar(array $dados): Apontamento;

    public function buscarPorId(int $id): ?Apontamento;

    public function buscarApontamentoAtivo(SessaoTrabalho $sessao): ?Apontamento;

    public function pilhaJaBipada(int $etapaFluxoId, string $codPeca, string $ordemLote, int $pilha): bool;

    public function somarQtdProduzida(int $etapaFluxoId, string $ordemLote): int;

    public function historicoPorOperario(int $operarioId): Collection;
}
