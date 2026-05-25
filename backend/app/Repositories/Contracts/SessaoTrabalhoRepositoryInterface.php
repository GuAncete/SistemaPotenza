<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Operario;
use App\Models\SessaoTrabalho;

interface SessaoTrabalhoRepositoryInterface
{
    public function criarSessao(int $operarioId, int $maquinaId): SessaoTrabalho;

    public function encerrarSessao(SessaoTrabalho $sessao): SessaoTrabalho;

    public function buscarSessaoAtiva(Operario $operario): ?SessaoTrabalho;

    public function encerrarSessoesAtivas(Operario $operario): void;
}
