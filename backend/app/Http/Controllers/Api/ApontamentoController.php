<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Apontamento\BiparRequest;
use App\Http\Requests\Apontamento\FinalizarRequest;
use App\Http\Resources\ApontamentoResource;
use App\Http\Traits\ApiResponseTrait;
use App\Repositories\Contracts\ApontamentoRepositoryInterface;
use App\Services\ApontamentoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApontamentoController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly ApontamentoService             $apontamentoService,
        private readonly ApontamentoRepositoryInterface $apontamentoRepo,
    ) {}

    public function bipar(BiparRequest $request): JsonResponse
    {
        $apontamento = $this->apontamentoService->bipar(
            $request->user()->operario,
            $request->validated()
        );

        return $this->successResponse(
            new ApontamentoResource($apontamento),
            'Ficha bipada com sucesso.',
            201
        );
    }

    public function iniciarSetup(Request $request, int $id): JsonResponse
    {
        $apontamento = $this->apontamentoRepo->buscarPorId($id);

        if (! $apontamento) {
            return $this->errorResponse('Apontamento não encontrado.', 404);
        }

        $this->authorize('update', $apontamento);

        $etapa = $this->apontamentoService->iniciarSetup($apontamento);

        return $this->successResponse(['etapa_producao' => $etapa], 'Setup iniciado.');
    }

    public function iniciarProducao(Request $request, int $id): JsonResponse
    {
        $apontamento = $this->apontamentoRepo->buscarPorId($id);

        if (! $apontamento) {
            return $this->errorResponse('Apontamento não encontrado.', 404);
        }

        $this->authorize('update', $apontamento);

        $etapa = $this->apontamentoService->iniciarProducao($apontamento);

        return $this->successResponse(['etapa_producao' => $etapa], 'Produção iniciada.');
    }

    public function finalizar(FinalizarRequest $request, int $id): JsonResponse
    {
        $apontamento = $this->apontamentoRepo->buscarPorId($id);

        if (! $apontamento) {
            return $this->errorResponse('Apontamento não encontrado.', 404);
        }

        $this->authorize('update', $apontamento);

        $result = $this->apontamentoService->finalizar(
            $apontamento,
            $request->validated()['qtd_produzida']
        );

        return $this->successResponse(
            new ApontamentoResource($result),
            'Apontamento finalizado com sucesso.'
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $apontamento = $this->apontamentoRepo->buscarPorId($id);

        if (! $apontamento) {
            return $this->errorResponse('Apontamento não encontrado.', 404);
        }

        $this->authorize('view', $apontamento);

        return $this->successResponse(new ApontamentoResource($apontamento));
    }

    public function historico(Request $request): JsonResponse
    {
        $apontamentos = $this->apontamentoRepo->historicoPorOperario(
            $request->user()->operario->id
        );

        return $this->successResponse(
            ApontamentoResource::collection($apontamentos),
            'Histórico de apontamentos.'
        );
    }
}
