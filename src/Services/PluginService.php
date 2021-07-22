<?php


namespace Latus\Plugins\Services;


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Latus\Helpers\Paths;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Plugins\Models\Plugin;
use Latus\Plugins\Repositories\Contracts\PluginRepository;

class PluginService
{

    public static array $create_validation_rules = [
        'name' => 'required|string|min:5',
        'proxy_name' => 'sometimes|string|nullable',
        'status' => 'required|integer|between:0,4',
        'repository_id' => 'sometimes|nullable|exists:composer_repositories,id',
        'target_version' => 'sometimes|string|min:1',
        'current_version' => 'sometimes|string|min:1'
    ];

    public static array $update_validation_rules = [
        'proxy_name' => 'sometimes|string|nullable',
        'status' => 'sometimes|integer|between:0,4',
        'repository_id' => 'sometimes|nullable|exists:composer_repositories,id',
        'target_version' => 'sometimes|string|min:1',
        'current_version' => 'sometimes|string|min:1'
    ];

    public function __construct(
        protected PluginRepository $pluginRepository
    )
    {
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function createPlugin(array $attributes): Model
    {
        $validator = Validator::make($attributes, self::$create_validation_rules);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        return $this->pluginRepository->create($attributes);
    }

    public function activatePlugin(Plugin $plugin)
    {
        $this->pluginRepository->activate($plugin);
    }

    public function deactivatePlugin(Plugin $plugin)
    {
        $this->pluginRepository->deactivate($plugin);
    }

    /**
     * @param bool $deleteFiles
     * @throws FileNotFoundException
     */
    public function deletePlugin(Plugin $plugin, bool $deleteFiles = false)
    {
        $plugin_name = $this->pluginRepository->getName($plugin);

        $this->pluginRepository->delete($plugin);

        if ($deleteFiles) {
            $files_dir = Paths::pluginPath($plugin_name);
            if (!Storage::deleteDirectory($files_dir)) {
                throw new FileNotFoundException('Directory "' . $files_dir . '" could not be deleted');
            }
        }
    }

    public function updatePlugin(Plugin $plugin, array $attributes)
    {
        $validator = Validator::make($attributes, self::$update_validation_rules);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        $this->pluginRepository->update($plugin, $attributes);
    }

    public function find(int|string $id): Model|null
    {
        return $this->pluginRepository->find($id);
    }

    public function findByName(string $name): Model|null
    {
        return $this->pluginRepository->findByName($name);
    }

    public function getComposerRepository(Plugin $plugin): Model
    {
        return $this->pluginRepository->getComposerRepository($plugin);
    }

    public function setComposerRepository(Plugin $plugin, ComposerRepository $composerRepository)
    {
        $this->pluginRepository->setComposerRepository($plugin, $composerRepository);
    }
}