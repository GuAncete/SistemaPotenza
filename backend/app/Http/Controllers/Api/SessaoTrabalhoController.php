<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sessao\IniciarSessaoRequest;
use App\Http\Resources\SessaoTrabalhoResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Maquina;
use App\Services\SessaoTrabalhoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessaoTrabalhoController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly SessaoTrabalhoService $sessaoService,
    ) {}

    public function disponiveis(): JsonResponse
    {
        $maquinas = Maquina::where('ativa', true)
            ->with('etapaFluxo')
            ->orderBy('nome')
            ->get()
            ->map(fn ($m) => [
                'id'          => $m->id,
                'nome'        => $m->nome,
                'descricao'   => $m->descricao,
                'etapa_fluxo' => [
                    'id'   => $m->etapaFluxo->id,
                    'nome' => $m->etapaFluxo->nome,
                ],
            ]);

        return $this->successResponse($maquinas, 'Máquinas disponíveis.');
    }

    public function iniciar(IniciarSessaoRequest $request): JsonResponse
    {
        $operario = $request->user()->operario;
        $sessao   = $this->sessaoService->iniciar($operario, $request->validated()['maquina_id']);

        return $this->successResponse(
            new SessaoTrabalhoResource($sessao),
            'Sessão iniciada com sucesso.',
            201
        );
    }

    public function encerrar(Request $request): JsonResponse
    {
        $this->sessaoService->encerrar($request->user()->operario);

        return $this->successResponse(null, 'Sessão encerrada com sucesso.');
    }

    public function ativa(Request $request): JsonResponse
    {
        $sessao = $this->sessaoService->ativa($request->user()->operario);

        if (! $sessao) {
            return $this->errorResponse('Nenhuma sessão ativa.', 404);
        }

        return $this->successResponse(new SessaoTrabalhoResource($sessao), 'Sessão ativa.');
    }
}
