<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DealService;
use Illuminate\Support\Facades\Log;

class DealServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(DealService::class, function ($app) {
            return new DealService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Log::info('DealService был загружен.');
    }
}
