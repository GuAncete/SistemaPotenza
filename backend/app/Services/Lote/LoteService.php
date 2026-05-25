<?php

declare(strict_types=1);

namespace App\Services\Lote;

use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\DB;

class LoteService implements LoteServiceInterface
{
    public function buscarPorOrdemLote(string $ordemLote): array
    {
        $row = DB::connection('terceirizado')
            ->selectOne(
                'SELECT
                    Empresa, Lote, DataEmbalagem, Prod_Codi, CodiSemiAcabado,
                    DenoSemiAcabado, SubgSemiAcabado, TipoMate, Espess, Comp,
                    Larg, QtdBorComp, QtdBorLarg, Pintura, CorCopo,
                    Qtde_Prod, QtdeSemi, Qtde_Total
                 FROM [db1Fabri].[dbo].[FbmLoteFichaTecnica]
                 WHERE Lote = ?',
                [$ordemLote]
            );

        if (! $row) {
            throw new BusinessException("Lote '{$ordemLote}' não encontrado no sistema.", 422);
        }

        return $this->mapear((array) $row);
    }

    private function mapear(array $row): array
    {
        return [
            'lote'              => $row['Lote'],
            'cod_produto'       => (string) ($row['Prod_Codi'] ?? ''),
            'cod_peca'          => (string) ($row['CodiSemiAcabado'] ?? ''),
            'desc_peca'         => (string) ($row['DenoSemiAcabado'] ?? ''),
            'qtde_total'        => (int) ($row['Qtde_Total'] ?? 0),
            'empresa'           => $row['Empresa'] ?? null,
            'data_embalagem'    => $row['DataEmbalagem'] ?? null,
            'subg_semi_acabado' => $row['SubgSemiAcabado'] ?? null,
            'tipo_mate'         => $row['TipoMate'] ?? null,
            'espess'            => $row['Espess'] ?? null,
            'comp'              => $row['Comp'] ?? null,
            'larg'              => $row['Larg'] ?? null,
            'qtd_bor_comp'      => $row['QtdBorComp'] ?? null,
            'qtd_bor_larg'      => $row['QtdBorLarg'] ?? null,
            'pintura'           => $row['Pintura'] ?? null,
            'cor_copo'          => $row['CorCopo'] ?? null,
            'qtde_prod'         => isset($row['Qtde_Prod']) ? (int) $row['Qtde_Prod'] : null,
            'qtde_semi'         => isset($row['QtdeSemi']) ? (int) $row['QtdeSemi'] : null,
        ];
    }
}
