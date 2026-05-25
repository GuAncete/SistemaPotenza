<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Apontamento;
use App\Models\SessaoTrabalho;
use App\Policies\ApontamentoPolicy;
use App\Policies\SessaoTrabalhoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Apontamento::class    => ApontamentoPolicy::class,
        SessaoTrabalho::class => SessaoTrabalhoPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('is-operario', fn ($user) => $user->role === 'operario');
        Gate::define('is-gestor', fn ($user) => in_array($user->role, ['gestor', 'admin'], true));
        Gate::define('is-admin', fn ($user) => $user->role === 'admin');
    }
}
