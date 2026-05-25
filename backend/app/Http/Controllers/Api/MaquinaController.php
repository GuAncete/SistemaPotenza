<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Maquina;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaquinaController extends Controller
{
    use ApiResponseTrait;

    public function index(): JsonResponse
    {
        return $this->successResponse(Maquina::with('etapaFluxo')->orderBy('nome')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'etapa_fluxo_id' => ['required', 'integer', 'exists:etapas_fluxo,id'],
            'nome'           => ['required', 'string', 'max:100'],
            'codigo'         => ['nullable', 'string', 'max:50', 'unique:maquinas,codigo'],
            'ano'            => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'descricao'      => ['nullable', 'string'],
            'ativa'          => ['boolean'],
            'foto'           => ['sometimes', 'nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('maquinas', 'public');
        }

        return $this->successResponse(
            Maquina::create($data)->load('etapaFluxo'),
            'Máquina criada.',
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $maquina = Maquina::with('etapaFluxo')->find($id);

        return $maquina
            ? $this->successResponse($maquina)
            : $this->errorResponse('Máquina não encontrada.', 404);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $maquina = Maquina::find($id);

        if (! $maquina) {
            return $this->errorResponse('Máquina não encontrada.', 404);
        }

        $data = $request->validate([
            'etapa_fluxo_id' => ['sometimes', 'integer', 'exists:etapas_fluxo,id'],
            'nome'           => ['sometimes', 'string', 'max:100'],
            'codigo'         => ['nullable', 'string', 'max:50', 'unique:maquinas,codigo,' . $id],
            'ano'            => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'descricao'      => ['nullable', 'string'],
            'ativa'          => ['boolean'],
            'foto'           => ['sometimes', 'nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($maquina->foto) {
                Storage::disk('public')->delete($maquina->foto);
            }
            $data['foto'] = $request->file('foto')->store('maquinas', 'public');
        }

        $maquina->update($data);

        return $this->successResponse($maquina->load('etapaFluxo'), 'Máquina atualizada.');
    }

    public function destroy(int $id): JsonResponse
    {
        $maquina = Maquina::find($id);

        if (! $maquina) {
            return $this->errorResponse('Máquina não encontrada.', 404);
        }

        $maquina->update(['ativa' => false]);

        return $this->successResponse($maquina->load('etapaFluxo'), 'Máquina desativada.');
    }
}
