<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessaoTrabalhoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'inicio'  => $this->inicio?->toIso8601String(),
            'fim'     => $this->fim?->toIso8601String(),
            'ativa'   => $this->isAtiva(),
            'maquina' => $this->whenLoaded('maquina', fn () => [
                'id'          => $this->maquina->id,
                'nome'        => $this->maquina->nome,
                'etapa_fluxo' => $this->when(
                    $this->maquina->relationLoaded('etapaFluxo'),
                    fn () => [
                        'id'    => $this->maquina->etapaFluxo->id,
                        'nome'  => $this->maquina->etapaFluxo->nome,
                        'ordem' => $this->maquina->etapaFluxo->ordem,
                    ]
                ),
            ]),
        ];
    }
}
