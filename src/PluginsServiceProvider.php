<?php

namespace Latus\Plugins;

use Illuminate\Support\ServiceProvider;
use Latus\Plugins\Repositories\Contracts\PluginRepository as PluginRepositoryContract;
use Latus\Plugins\Repositories\Eloquent\PluginRepository;
use Latus\Plugins\Repositories\Contracts\ComposerRepositoryRepository as ComposerRepositoryRepositoryContract;
use Latus\Plugins\Repositories\Eloquent\ComposerRepositoryRepository;
use Latus\Plugins\Repositories\Contracts\ThemeRepository as ThemeRepositoryContract;
use Latus\Plugins\Repositories\Eloquent\ThemeRepository;

class PluginsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        if (!$this->app->bound(PluginRepositoryContract::class)) {
            $this->app->bind(PluginRepositoryContract::class, PluginRepository::class);
        }

        if (!$this->app->bound(ComposerRepositoryRepositoryContract::class)) {
            $this->app->bind(ComposerRepositoryRepositoryContract::class, ComposerRepositoryRepository::class);
        }

        if (!$this->app->bound(ThemeRepositoryContract::class)) {
            $this->app->bind(ThemeRepositoryContract::class, ThemeRepository::class);
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/latus-plugins.php', 'latus-plugins');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
