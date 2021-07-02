<?php


namespace Latus\Plugins\Repositories\Eloquent;


use Latus\Plugins\Models\Plugin;
use Latus\Plugins\Repositories\Contracts\PluginRepository as PluginRepositoryContract;
use Latus\Repositories\EloquentRepository;

class PluginRepository extends EloquentRepository implements PluginRepositoryContract
{

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
}