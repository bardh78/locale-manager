<?php

namespace Autoluminescent\LocaleManager;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LocaleManagerServiceProvider extends ServiceProvider
{
    public function boot(Router $router): void
    {
        $this->app->singleton(LocaleManager::class);
        $router->aliasMiddleware('locale', SetLocale::class);
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('locale.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations')
            ], 'migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'locale');
    }
}
