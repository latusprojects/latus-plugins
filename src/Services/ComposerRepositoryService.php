<?php


namespace Latus\Plugins\Services;


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Latus\Plugins\Models\Plugin;
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

    public function activateRepository(Plugin $plugin)
    {
        $this->composerRepositoryRepository->activate($plugin);
    }

    public function deactivateRepository(Plugin $plugin)
    {
        $this->composerRepositoryRepository->deactivate($plugin);
    }

    /**
     * @param bool $deleteFiles
     * @throws FileNotFoundException
     */
    public function deleteRepository(Plugin $plugin, bool $deleteFiles = false)
    {
        $plugin_name = $this->composerRepositoryRepository->getName($plugin);

        $this->composerRepositoryRepository->delete($plugin);

        if ($deleteFiles) {
            $files_dir = config('latus-plugins.plugins_dir') . '/' . $plugin_name;
            if (!Storage::deleteDirectory($files_dir)) {
                throw new FileNotFoundException('Directory "' . $files_dir . '" could not be deleted');
            }
        }
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
}