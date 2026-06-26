<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\OrdemManutencao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManutencaoAdminController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = OrdemManutencao::with('maquina.etapaFluxo')
            ->join('maquinas', 'maquinas.id', '=', 'ordens_manutencao.maquina_id')
            ->select('ordens_manutencao.*')
            ->orderByRaw("CASE maquinas.prioridade
                WHEN 'critica' THEN 1
                WHEN 'alta'    THEN 2
                WHEN 'normal'  THEN 3
                WHEN 'baixa'   THEN 4
                ELSE 5
            END")
            ->orderBy('ordens_manutencao.solicitado_em', 'asc');

        if ($request->filled('status')) {
            $query->where('ordens_manutencao.status', $request->status);
        }

        if ($request->filled('maquina_id')) {
            $query->where('ordens_manutencao.maquina_id', (int) $request->maquina_id);
        }

        if ($request->filled('etapa_fluxo_id')) {
            $query->where('maquinas.etapa_fluxo_id', (int) $request->etapa_fluxo_id);
        }

        if ($request->filled('data')) {
            $query->whereDate('ordens_manutencao.solicitado_em', $request->data);
        }

        return $this->successResponse($query->get());
    }

    public function show(int $id): JsonResponse
    {
        $ordem = OrdemManutencao::with('maquina.etapaFluxo')->find($id);

        if (! $ordem) {
            return $this->errorResponse('Ordem de manutenção não encontrada.', 404);
        }

        return $this->successResponse($ordem);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $ordem = OrdemManutencao::find($id);

        if (! $ordem) {
            return $this->errorResponse('Ordem de manutenção não encontrada.', 404);
        }

        $data = $request->validate([
            'status'      => ['sometimes', 'string', 'in:aberta,em_atendimento,pausada,concluida,cancelada'],
            'observacoes' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ]);

        if (isset($data['status'])) {
            // Set atendido_em only on the first transition to em_atendimento
            if ($data['status'] === 'em_atendimento' && is_null($ordem->atendido_em)) {
                $data['atendido_em'] = now();
            }

            if (in_array($data['status'], ['concluida', 'cancelada'], true) && is_null($ordem->concluido_em)) {
                $data['concluido_em'] = now();
            }
        }

        $ordem->update($data);

        return $this->successResponse($ordem->load('maquina.etapaFluxo'), 'Ordem atualizada.');
    }
}
