<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DatabaseFallbackService;

class DatabaseFallbackServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DatabaseFallbackService::class, function ($app) {
            return new DatabaseFallbackService();
        });
    }

    public function boot()
    {
        $fallbackService = $this->app->make(DatabaseFallbackService::class);

        if (!$fallbackService->isConnected()) {
            $this->app['view']->share('database_offline', true);
            $this->app['view']->share('demo_mode', config('database_fallback.demo_mode', false));
        }

        if (!file_exists(config('database_fallback.storage_path'))) {
            mkdir(config('database_fallback.storage_path'), 0755, true);
        }
    }
}