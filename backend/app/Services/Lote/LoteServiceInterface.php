<?php

declare(strict_types=1);

namespace App\Services\Lote;

interface LoteServiceInterface
{
    /**
     * Busca os dados técnicos do lote pelo OrdemLote.
     *
     * Retorna array com: lote, cod_produto, cod_peca, desc_peca, qtde_total e campos adicionais.
     */
    public function buscarPorOrdemLote(string $ordemLote): array;
}
