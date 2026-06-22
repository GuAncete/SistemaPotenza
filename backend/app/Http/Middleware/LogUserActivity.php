<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class LogUserActivity
{
    private const READ_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function __construct(private readonly ActivityLogService $activityLog) {}

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (in_array($request->method(), self::READ_METHODS, true)) {
            return;
        }

        $path = $request->path();

        foreach (config('activity_log.excluded_path_prefixes', []) as $prefix) {
            if (Str::startsWith($path, $prefix)) {
                return;
            }
        }

        $action = $request->route()?->getName() ?? ($request->method() . ' /' . $path);

        $this->activityLog->record(
            user: $request->user(),
            action: $action,
            description: $request->method() . ' /' . $path,
            request: $request,
            method: $request->method(),
            route: '/' . $path,
            payload: $request->except(config('activity_log.excluded_payload_keys', [])),
            statusCode: $response->getStatusCode(),
        );
    }
}
