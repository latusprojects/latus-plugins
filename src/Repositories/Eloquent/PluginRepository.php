<?php


namespace Latus\Plugins\Repositories\Eloquent;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Latus\Plugins\Models\Plugin;
use Latus\Plugins\Repositories\Contracts\PluginRepository as PluginRepositoryContract;
use Latus\Repositories\EloquentRepository;

class PluginRepository extends EloquentRepository implements PluginRepositoryContract
{

    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin);
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

    public function findByName(string $name): Model
    {
        return Plugin::where('name', $name)->first();
    }
}