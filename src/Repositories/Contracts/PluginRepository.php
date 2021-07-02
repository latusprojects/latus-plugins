<?php


namespace Latus\Plugins\Repositories\Contracts;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Latus\Plugins\Models\Plugin;
use Latus\Repositories\Contracts\Repository;

interface PluginRepository extends Repository
{

    public function __construct(Plugin $plugin);

    public function activate(): void;

    public function deactivate(): void;

    public function delete();

    public function getName(): string;

    public function getAllActive(): Collection;

    public function findByName(string $name): Model;
}