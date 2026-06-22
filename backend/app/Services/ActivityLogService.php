<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function record(
        ?User $user,
        string $action,
        string $description,
        ?Request $request = null,
        ?string $method = null,
        ?string $route = null,
        ?array $payload = null,
        ?int $statusCode = null,
    ): void {
        ActivityLog::create([
            'user_id'     => $user?->id,
            'user_name'   => $user?->name ?? 'Anônimo',
            'action'      => $action,
            'method'      => $method,
            'route'       => $route,
            'description' => $description,
            'payload'     => $payload,
            'status_code' => $statusCode,
            'ip_address'  => $request?->ip(),
            'created_at'  => now(),
        ]);
    }
}
