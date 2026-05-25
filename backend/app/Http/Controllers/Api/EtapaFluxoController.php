<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\EtapaFluxo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EtapaFluxoController extends Controller
{
    use ApiResponseTrait;

    public function index(): JsonResponse
    {
        return $this->successResponse(EtapaFluxo::orderBy('ordem')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $etapa = EtapaFluxo::create($request->validate([
            'nome'  => ['required', 'string', 'max:100'],
            'ordem' => ['required', 'integer', 'min:1'],
            'ativa' => ['boolean'],
        ]));

        return $this->successResponse($etapa, 'Etapa criada.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $etapa = EtapaFluxo::find($id);

        return $etapa
            ? $this->successResponse($etapa)
            : $this->errorResponse('Etapa não encontrada.', 404);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $etapa = EtapaFluxo::find($id);

        if (! $etapa) {
            return $this->errorResponse('Etapa não encontrada.', 404);
        }

        $etapa->update($request->validate([
            'nome'  => ['sometimes', 'string', 'max:100'],
            'ordem' => ['sometimes', 'integer', 'min:1'],
            'ativa' => ['boolean'],
        ]));

        return $this->successResponse($etapa, 'Etapa atualizada.');
    }

    public function destroy(int $id): JsonResponse
    {
        $etapa = EtapaFluxo::find($id);

        if (! $etapa) {
            return $this->errorResponse('Etapa não encontrada.', 404);
        }

        $etapa->delete();

        return $this->successResponse(null, 'Etapa removida.');
    }
}
