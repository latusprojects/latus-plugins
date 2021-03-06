<?php


namespace Latus\Plugins\Repositories\Contracts;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Repositories\Contracts\Repository;

interface ComposerRepositoryRepository extends Repository
{

    public function activate(ComposerRepository $composerRepository): void;

    public function deactivate(ComposerRepository $composerRepository): void;

    public function delete(ComposerRepository $composerRepository);

    public function getName(ComposerRepository $composerRepository): string;

    public function findByName(string $name): Model|null;

    public function findByUrl(string|null $url): Model|null;

    public function getPlugins(ComposerRepository $composerRepository): Collection;

    public function getThemes(ComposerRepository $composerRepository): Collection;

}