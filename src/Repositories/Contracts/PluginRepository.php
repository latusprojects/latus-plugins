<?php


namespace Latus\Plugins\Repositories\Contracts;


use Latus\Repositories\Contracts\Repository;

interface PluginRepository extends Repository
{
    public function activate(): void;

    public function deactivate(): void;

    public function delete();

    public function getName(): string;
}