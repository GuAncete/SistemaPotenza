<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Services\KanbanService;
use Illuminate\Http\JsonResponse;

class KanbanController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly KanbanService $kanbanService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->successResponse(
            $this->kanbanService->kanban(),
            'Kanban carregado.'
        );
    }

    public function lotesEtapa(int $etapaFluxoId): JsonResponse
    {
        return $this->successResponse(
            $this->kanbanService->lotesEtapa($etapaFluxoId),
            'Lotes da etapa.'
        );
    }

    public function historicoLote(string $ordemLote): JsonResponse
    {
        return $this->successResponse(
            $this->kanbanService->historicoLote($ordemLote),
            'Histórico do lote.'
        );
    }
}
