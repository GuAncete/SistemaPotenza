<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ApontamentoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EtapaFluxoController;
use App\Http\Controllers\Api\KanbanController;
use App\Http\Controllers\Api\MaquinaController;
use App\Http\Controllers\Api\OperarioController;
use App\Http\Controllers\Api\SessaoTrabalhoController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout',          [AuthController::class, 'logout']);
        Route::get('/me',               [AuthController::class, 'me']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });
});

Route::middleware(['auth:sanctum', 'check_password_change', 'role:operario'])->group(function () {
    Route::get('/maquinas/disponiveis', [SessaoTrabalhoController::class, 'disponiveis']);

    Route::prefix('sessao')->group(function () {
        Route::post('/iniciar',  [SessaoTrabalhoController::class, 'iniciar']);
        Route::post('/encerrar', [SessaoTrabalhoController::class, 'encerrar']);
        Route::get('/ativa',     [SessaoTrabalhoController::class, 'ativa']);
    });

    Route::prefix('apontamento')->group(function () {
        Route::post('/bipar',                 [ApontamentoController::class, 'bipar']);
        Route::get('/historico',              [ApontamentoController::class, 'historico']);
        Route::get('/{id}',                   [ApontamentoController::class, 'show']);
        Route::post('/{id}/iniciar-setup',    [ApontamentoController::class, 'iniciarSetup']);
        Route::post('/{id}/iniciar-producao', [ApontamentoController::class, 'iniciarProducao']);
        Route::post('/{id}/finalizar',        [ApontamentoController::class, 'finalizar']);
    });
});

Route::middleware(['auth:sanctum', 'check_password_change', 'role:gestor,admin'])->prefix('kanban')->group(function () {
    Route::get('/',                           [KanbanController::class, 'index']);
    Route::get('/{etapaFluxoId}/lotes',       [KanbanController::class, 'lotesEtapa']);
    Route::get('/lote/{ordemLote}/historico', [KanbanController::class, 'historicoLote']);
});

Route::middleware(['auth:sanctum', 'check_password_change', 'role:admin'])->group(function () {
    Route::apiResource('maquinas',     MaquinaController::class);
    Route::apiResource('operarios',    OperarioController::class);
    Route::apiResource('etapas-fluxo', EtapaFluxoController::class);
});
