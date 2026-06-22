<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Excluded Path Prefixes
    |--------------------------------------------------------------------------
    |
    | Requests whose path starts with one of these prefixes are not logged
    | by App\Http\Middleware\LogUserActivity. Apontamento/sessao routes are
    | excluded because they already have their own granular event tracking
    | (SessaoTrabalho/EventoSessao).
    |
    */

    'excluded_path_prefixes' => [
        'api/apontamento',
        'api/sessao',
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded Payload Keys
    |--------------------------------------------------------------------------
    |
    | These request input keys are stripped before the payload is persisted
    | to activity_logs, regardless of which route received them.
    |
    */

    'excluded_payload_keys' => [
        'password',
        'current_password',
        'new_password',
        'password_confirmation',
        'token',
    ],

];
