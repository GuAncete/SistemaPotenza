<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApontamentoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'cod_peca'        => $this->cod_peca,
            'ordem_lote'      => $this->ordem_lote,
            'qtd_peca'        => $this->qtd_peca,
            'pilha'           => $this->pilha,
            'desc_peca'       => $this->desc_peca,
            'cod_produto'     => $this->cod_produto,
            'qtd_produzida'   => $this->qtd_produzida,
            'status'          => $this->status,
            'etapa_fluxo'     => $this->whenLoaded('etapaFluxo', fn () => [
                'id'   => $this->etapaFluxo->id,
                'nome' => $this->etapaFluxo->nome,
            ]),
            'etapas_producao' => $this->whenLoaded('etapasProducao', fn () =>
                $this->etapasProducao->map(fn ($ep) => [
                    'id'               => $ep->id,
                    'tipo'             => $ep->tipo,
                    'inicio'           => $ep->inicio?->toIso8601String(),
                    'fim'              => $ep->fim?->toIso8601String(),
                    'duracao_segundos' => $ep->duracao_segundos,
                ])
            ),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
