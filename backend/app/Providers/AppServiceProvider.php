<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Lote\LoteService;
use App\Services\Lote\LoteServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoteServiceInterface::class, LoteService::class);
    }

    public function boot(): void {}
}
