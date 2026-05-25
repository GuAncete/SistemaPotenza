<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Maquina;
use App\Models\Operario;
use App\Models\SessaoTrabalho;
use App\Repositories\Contracts\SessaoTrabalhoRepositoryInterface;

class SessaoTrabalhoService
{
    public function __construct(
        private readonly SessaoTrabalhoRepositoryInterface $sessaoRepo,
    ) {}

    public function iniciar(Operario $operario, int $maquinaId): SessaoTrabalho
    {
        $maquina = Maquina::where('id', $maquinaId)->where('ativa', true)->first();

        if (! $maquina) {
            throw new BusinessException('Máquina não encontrada ou inativa.', 422);
        }

        $this->sessaoRepo->encerrarSessoesAtivas($operario);

        $sessao = $this->sessaoRepo->criarSessao($operario->id, $maquinaId);

        return $sessao->load(['maquina.etapaFluxo']);
    }

    public function encerrar(Operario $operario): void
    {
        $sessao = $this->sessaoRepo->buscarSessaoAtiva($operario);

        if (! $sessao) {
            throw new BusinessException('Nenhuma sessão ativa encontrada.', 422);
        }

        $this->sessaoRepo->encerrarSessao($sessao);
    }

    public function ativa(Operario $operario): ?SessaoTrabalho
    {
        return $this->sessaoRepo->buscarSessaoAtiva($operario);
    }
}
