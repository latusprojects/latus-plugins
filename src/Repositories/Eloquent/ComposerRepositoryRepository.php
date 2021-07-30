<?php


namespace Latus\Plugins\Repositories\Eloquent;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Plugins\Repositories\Contracts\ComposerRepositoryRepository as ComposerRepositoryRepositoryContract;
use Latus\Repositories\EloquentRepository;

class ComposerRepositoryRepository extends EloquentRepository implements ComposerRepositoryRepositoryContract
{

    public function relatedModel(): Model
    {
        return new ComposerRepository();
    }

    public function activate(ComposerRepository $composerRepository): void
    {
        $composerRepository->status = ComposerRepository::STATUS_ACTIVATED;
        $composerRepository->save();
    }

    public function deactivate(ComposerRepository $composerRepository): void
    {
        $composerRepository->status = ComposerRepository::STATUS_DEACTIVATED;
        $composerRepository->save();
    }

    public function delete(ComposerRepository $composerRepository)
    {
        $composerRepository->delete();
    }

    public function getName(ComposerRepository $composerRepository): string
    {
        return $composerRepository->name;
    }

    public function getAllActive(): Collection
    {
        return ComposerRepository::where('status', ComposerRepository::STATUS_ACTIVATED)->get();
    }

    public function findByName(string $name): Model|null
    {
        return ComposerRepository::where('name', $name)->first();
    }

    public function findByUrl(string|null $url): Model|null
    {
        return ComposerRepository::where('url', $url)->first();
    }

    public function getPlugins(ComposerRepository $composerRepository): Collection
    {
        return $composerRepository->plugins()->get();
    }

    public function getThemes(ComposerRepository $composerRepository): Collection
    {
        return $composerRepository->themes()->get();
    }
}