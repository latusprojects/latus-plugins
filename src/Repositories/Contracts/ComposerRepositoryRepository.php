<?php


namespace Latus\Plugins\Repositories\Contracts;


use Illuminate\Database\Eloquent\Model;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Repositories\Contracts\Repository;

interface ComposerRepositoryRepository extends Repository
{

    public function __construct(ComposerRepository $composerRepository);

    public function activate(ComposerRepository $composerRepository): void;

    public function deactivate(ComposerRepository $composerRepository): void;

    public function delete(ComposerRepository $composerRepository);

    public function getName(ComposerRepository $composerRepository): string;

    public function findByName(string $name): Model|null;

    public function findByUrl(string|null $url): Model|null;
}