<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * 
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService();
        });
    }


    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Log::info('AuthService был загружен.');
    }
}
