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

    public function activate(): void
    {
        $this->model->status = Plugin::STATUS_ACTIVATED;
        $this->model->save();
    }

    public function deactivate(): void
    {
        $this->model->status = Plugin::STATUS_DEACTIVATED;
        $this->model->save();
    }

    public function delete()
    {
        $this->model->delete();
    }

    public function getName(): string
    {
        return $this->model->name;
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