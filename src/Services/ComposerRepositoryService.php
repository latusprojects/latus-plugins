<?php


namespace Latus\Plugins\Services;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Plugins\Repositories\Contracts\ComposerRepositoryRepository;

class ComposerRepositoryService
{

    public static array $create_validation_rules = [
        'name' => 'required|string|min:5',
        'status' => 'required|integer|between:0,2',
        'type' => 'sometimes|string|in:composer,vcs,path',
        //TODO: Create validation rule to accept both relative files paths and urls
        'url' => 'required|string|min:5'
    ];

    public function __construct(
        protected ComposerRepositoryRepository $composerRepositoryRepository
    )
    {
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function createRepository(array $attributes): Model
    {
        $validator = Validator::make($attributes, self::$create_validation_rules);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        return $this->composerRepositoryRepository->create($attributes);
    }

    public function activateRepository(ComposerRepository $composerRepository)
    {
        $this->composerRepositoryRepository->activate($composerRepository);
    }

    public function deactivateRepository(ComposerRepository $composerRepository)
    {
        $this->composerRepositoryRepository->deactivate($composerRepository);
    }

    public function deleteRepository(ComposerRepository $composerRepository)
    {
        $this->composerRepositoryRepository->delete($composerRepository);
    }

    public function find(int|string $id): Model|null
    {
        return $this->composerRepositoryRepository->find($id);
    }

    public function findByName(string $name): Model|null
    {
        return $this->composerRepositoryRepository->findByName($name);
    }

    public function findByUrl(string|null $url): Model|null
    {
        return $this->composerRepositoryRepository->findByUrl($url);
    }

    public function getPlugins(ComposerRepository $composerRepository): Collection
    {
        return $this->composerRepositoryRepository->getPlugins($composerRepository);
    }

    public function getThemes(ComposerRepository $composerRepository): Collection
    {
        return $this->composerRepositoryRepository->getThemes($composerRepository);
    }
}