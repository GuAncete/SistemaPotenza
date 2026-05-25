<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return $this->successResponse(
            [
                'user'                     => new UserResource($result['user']),
                'token'                    => $result['token'],
                'requires_password_change' => $result['requires_password_change'],
            ],
            'Login realizado com sucesso.'
        );
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $data = $request->validated();

        $this->authService->changePassword(
            $request->user(),
            $data['current_password'],
            $data['password']
        );

        return $this->successResponse(null, 'Senha alterada com sucesso.');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->successResponse(null, 'Logout realizado com sucesso.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($request->user()->load('operario')),
            'Dados do usuário autenticado.'
        );
    }
}
