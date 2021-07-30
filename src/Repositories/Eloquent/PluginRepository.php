<?php


namespace Latus\Plugins\Repositories\Eloquent;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Latus\Helpers\Paths;
use Latus\Plugins\Composer\ProxyPackage;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Plugins\Models\Plugin;
use Latus\Plugins\Repositories\Contracts\PluginRepository as PluginRepositoryContract;
use Latus\Repositories\EloquentRepository;

class PluginRepository extends EloquentRepository implements PluginRepositoryContract
{

    public function relatedModel(): Model
    {
        return new Plugin();
    }

    public function activate(Plugin $plugin): void
    {
        $plugin->status = Plugin::STATUS_ACTIVATED;
        $plugin->save();
    }

    public function deactivate(Plugin $plugin): void
    {
        $plugin->status = Plugin::STATUS_DEACTIVATED;
        $plugin->save();
    }

    public function delete(Plugin $plugin)
    {
        $plugin->delete();
    }

    public function getName(Plugin $plugin): string
    {
        return $plugin->name;
    }

    public function getAllActive(): Collection
    {
        return Plugin::where('status', Plugin::STATUS_ACTIVATED)->get();
    }

    public function findByName(string $name): Model|null
    {
        return Plugin::where('name', $name)->first();
    }

    public function update(Plugin $plugin, array $attributes)
    {
        $plugin->update($attributes);
    }

    public function getComposerRepository(Plugin $plugin): Model
    {
        return $plugin->repository()->first();
    }

    public function setComposerRepository(Plugin $plugin, ComposerRepository $composerRepository)
    {
        $plugin->repository()->associate($composerRepository);
    }

    public function rollbackMigrations(Plugin $plugin)
    {
        $migrations_path = Paths::pluginPath(
            ProxyPackage::PREFIX . $plugin->name . DIRECTORY_SEPARATOR .
            'database' . DIRECTORY_SEPARATOR . 'migrations'
        );


        if (file_exists($migrations_path)) {
            Artisan::call('migrate:rollback', ['--path' => $migrations_path]);
        }
    }
}