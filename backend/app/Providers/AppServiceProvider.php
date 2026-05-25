<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Lote\LoteService;
use App\Services\Lote\LoteServiceInterface;
use App\Services\Lote\MockLoteService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoteServiceInterface::class, function () {
            if (empty(env('DB_TERCEIRIZADO_HOST'))) {
                return new MockLoteService();
            }

            return new LoteService();
        });
    }

    public function boot(): void {}
}
