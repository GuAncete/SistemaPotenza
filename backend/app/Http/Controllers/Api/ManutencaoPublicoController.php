<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Maquina;
use App\Models\OrdemManutencao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManutencaoPublicoController extends Controller
{
    use ApiResponseTrait;

    public function maquina(int $id): JsonResponse
    {
        $maquina = Maquina::where('id', $id)->where('ativa', true)->first();

        if (! $maquina) {
            return $this->errorResponse('Máquina não encontrada ou inativa.', 404);
        }

        return $this->successResponse([
            'id'         => $maquina->id,
            'nome'       => $maquina->nome,
            'codigo'     => $maquina->codigo,
            'prioridade' => $maquina->prioridade,
        ]);
    }

    public function solicitar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'maquina_id'  => ['required', 'integer', 'exists:maquinas,id'],
            'solicitante' => ['required', 'string', 'max:150'],
            'motivo'      => ['required', 'string', 'max:1000'],
        ]);

        $maquina = Maquina::where('id', $data['maquina_id'])->where('ativa', true)->first();

        if (! $maquina) {
            return $this->errorResponse('Máquina não encontrada ou inativa.', 422);
        }

        $ordem = OrdemManutencao::create([
            'maquina_id'    => $data['maquina_id'],
            'solicitante'   => trim($data['solicitante']),
            'motivo'        => trim($data['motivo']),
            'status'        => 'aberta',
            'solicitado_em' => now(),
        ]);

        $ordem->load('maquina');

        return $this->successResponse($ordem, 'Solicitação enviada com sucesso.', 201);
    }
}
