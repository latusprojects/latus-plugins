<?php


namespace Latus\Plugins\Services;


use http\Exception\InvalidArgumentException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Latus\Plugins\Repositories\Contracts\PluginRepository;

class PluginService
{

    public static array $create_validation_rules = [
        'name' => 'required|string|min:5',
        'status' => 'required|integer|between:0,1',
        'repository_id' => 'sometimes|exists:composer_repositories,id',
        'target_version' => 'sometimes|string|min:1'
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
            throw new InvalidArgumentException($validator->errors()->first());
        }

        return $this->pluginRepository->create($attributes);
    }

    public function activatePlugin()
    {
        $this->pluginRepository->activate();
    }

    public function deactivatePlugin()
    {
        $this->pluginRepository->deactivate();
    }

    /**
     * @param bool $deleteFiles
     * @throws FileNotFoundException
     */
    public function deletePlugin(bool $deleteFiles = false)
    {
        $plugin_name = $this->pluginRepository->getName();

        $this->pluginRepository->delete();

        if ($deleteFiles) {
            $files_dir = config('latus-plugins.plugins_dir') . '/' . $plugin_name;
            if (!Storage::deleteDirectory($files_dir)) {
                throw new FileNotFoundException('Directory "' . $files_dir . '" could not be deleted');
            }
        }
    }

    public function find(int|string $id): Model
    {
        return $this->pluginRepository->find($id);
    }
}