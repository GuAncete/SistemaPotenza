<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Apontamento;
use App\Models\EtapaProducao;
use App\Models\Operario;
use App\Repositories\Contracts\ApontamentoRepositoryInterface;
use App\Repositories\Contracts\HistoricoLoteRepositoryInterface;
use App\Repositories\Contracts\SessaoTrabalhoRepositoryInterface;
use App\Services\Lote\LoteServiceInterface;
use Carbon\Carbon;

class ApontamentoService
{
    public function __construct(
        private readonly ApontamentoRepositoryInterface    $apontamentoRepo,
        private readonly SessaoTrabalhoRepositoryInterface $sessaoRepo,
        private readonly HistoricoLoteRepositoryInterface  $historicoRepo,
        private readonly LoteServiceInterface              $loteService,
    ) {}

    public function bipar(Operario $operario, array $dados): Apontamento
    {
        $sessao = $this->sessaoRepo->buscarSessaoAtiva($operario);

        if (! $sessao) {
            throw new BusinessException('Operário não possui sessão ativa. Selecione uma máquina primeiro.', 422);
        }

        if ($this->apontamentoRepo->buscarApontamentoAtivo($sessao)) {
            throw new BusinessException('Já existe um apontamento em andamento. Finalize-o antes de bipar nova ficha.', 422);
        }

        $etapaFluxoId = $sessao->maquina->etapa_fluxo_id;
        $pilha        = (int) $dados['pilha'];

        if ($this->apontamentoRepo->pilhaJaBipada($etapaFluxoId, $dados['cod_peca'], $dados['ordem_lote'], $pilha)) {
            throw new BusinessException('Pilha já bipada nesta etapa. Não é possível registrar duplicidade.', 422);
        }

        $loteDados = $this->loteService->buscarPorOrdemLote($dados['ordem_lote']);

        return $this->apontamentoRepo->criar([
            'sessao_trabalho_id' => $sessao->id,
            'etapa_fluxo_id'     => $etapaFluxoId,
            'cod_peca'           => $dados['cod_peca'],
            'ordem_lote'         => $dados['ordem_lote'],
            'qtd_peca'           => (int) $dados['qtd_peca'],
            'pilha'              => $pilha,
            'desc_peca'          => $loteDados['desc_peca'],
            'cod_produto'        => $loteDados['cod_produto'],
            'status'             => 'em_setup',
        ]);
    }

    public function iniciarSetup(Apontamento $apontamento): EtapaProducao
    {
        if ($apontamento->status !== 'em_setup') {
            throw new BusinessException('Apontamento não está no status em_setup.', 422);
        }

        if ($apontamento->etapaSetup()->exists()) {
            throw new BusinessException('Setup já foi iniciado.', 422);
        }

        return EtapaProducao::create([
            'apontamento_id' => $apontamento->id,
            'tipo'           => 'setup',
            'inicio'         => Carbon::now(),
        ]);
    }

    public function iniciarProducao(Apontamento $apontamento): EtapaProducao
    {
        $setup = $apontamento->etapaSetup;

        if (! $setup || $setup->fim !== null) {
            throw new BusinessException('Setup não iniciado ou já finalizado.', 422);
        }

        $fim     = Carbon::now();
        $duracao = (int) $setup->inicio->diffInSeconds($fim);

        $setup->update(['fim' => $fim, 'duracao_segundos' => $duracao]);
        $apontamento->update(['status' => 'em_producao']);

        return EtapaProducao::create([
            'apontamento_id' => $apontamento->id,
            'tipo'           => 'producao',
            'inicio'         => $fim,
        ]);
    }

    public function finalizar(Apontamento $apontamento, int $qtdProduzida): Apontamento
    {
        $producao = $apontamento->etapaProducao;

        if (! $producao || $producao->fim !== null) {
            throw new BusinessException('Produção não iniciada ou já finalizada.', 422);
        }

        $fim     = Carbon::now();
        $duracao = (int) $producao->inicio->diffInSeconds($fim);

        $producao->update(['fim' => $fim, 'duracao_segundos' => $duracao]);

        $apontamento->update([
            'qtd_produzida' => $qtdProduzida,
            'status'        => 'finalizado',
        ]);

        $this->atualizarHistoricoLote($apontamento->fresh());

        return $apontamento->fresh()->load(['etapasProducao', 'etapaFluxo']);
    }

    private function atualizarHistoricoLote(Apontamento $apontamento): void
    {
        $historico = $this->historicoRepo->buscarOuCriar(
            $apontamento->etapa_fluxo_id,
            $apontamento->cod_peca,
            $apontamento->ordem_lote
        );

        $historico = $this->historicoRepo->incrementarPilhaConcluida($historico);

        $loteDados = $this->loteService->buscarPorOrdemLote($apontamento->ordem_lote);
        $totalProd = $this->apontamentoRepo->somarQtdProduzida(
            $apontamento->etapa_fluxo_id,
            $apontamento->ordem_lote
        );

        if ($totalProd >= $loteDados['qtde_total']) {
            $this->historicoRepo->concluir($historico);
        }
    }
}
