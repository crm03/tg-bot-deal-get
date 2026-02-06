<?php

namespace App\Providers;

use App\Services\TelegramUserService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class TelegramUserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TelegramUserService::class, function ($app) {
            Log::debug("TelegramUserService создан: ");
            return new TelegramUserService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Log::info('TelegramUserService был загружен.');
    }
}
