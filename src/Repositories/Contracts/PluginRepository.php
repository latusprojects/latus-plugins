<?php


namespace Latus\Plugins\Repositories\Contracts;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Plugins\Models\Plugin;
use Latus\Repositories\Contracts\Repository;

interface PluginRepository extends Repository
{

    public function activate(Plugin $plugin): void;

    public function deactivate(Plugin $plugin): void;

    public function delete(Plugin $plugin);

    public function getName(Plugin $plugin): string;

    public function getAllActive(): Collection;

    public function findByName(string $name): Model|null;

    public function update(Plugin $plugin, array $attributes);

    public function getComposerRepository(Plugin $plugin): Model;

    public function setComposerRepository(Plugin $plugin, ComposerRepository $composerRepository);
}