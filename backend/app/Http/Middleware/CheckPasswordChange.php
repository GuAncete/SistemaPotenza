<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->must_change_password === true) {
            return response()->json([
                'success' => false,
                'message' => 'Você precisa alterar sua senha antes de continuar.',
                'data'    => null,
                'errors'  => null,
            ], 403);
        }

        return $next($request);
    }
}
