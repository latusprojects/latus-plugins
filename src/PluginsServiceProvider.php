<?php

namespace Latus\Plugins;

use Illuminate\Support\ServiceProvider;
use Latus\Plugins\Models\Plugin;
use Latus\Plugins\Repositories\Contracts\PluginRepository as PluginRepositoryContract;
use Latus\Plugins\Repositories\Eloquent\PluginRepository;

class PluginsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if (!$this->app->bound(Plugin::class)) {
            $this->app->bind(Plugin::class, Plugin::class);
        }

        if (!$this->app->bound(PluginRepositoryContract::class)) {
            $this->app->bind(PluginRepositoryContract::class, PluginRepository::class);
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
