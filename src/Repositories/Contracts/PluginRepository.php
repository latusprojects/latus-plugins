<?php


namespace Latus\Plugins\Repositories\Contracts;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Latus\Plugins\Models\Plugin;
use Latus\Repositories\Contracts\Repository;

interface PluginRepository extends Repository
{

    public function __construct(Plugin $plugin);

    public function activate(Plugin $plugin): void;

    public function deactivate(Plugin $plugin): void;

    public function delete(Plugin $plugin);

    public function getName(Plugin $plugin): string;

    public function getAllActive(): Collection;

    public function findByName(string $name): Model;
}