<?php


namespace Latus\Plugins\Services;


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Plugins\Models\Theme;
use Latus\Plugins\Repositories\Contracts\ThemeRepository;
use Latus\Helpers\Paths;

class ThemeService
{

    public static array $create_validation_rules = [
        'name' => 'required|string|min:5',
        'status' => 'required|integer|between:0,4',
        'repository_id' => 'sometimes|nullable|exists:composer_repositories,id',
        'target_version' => 'sometimes|string|min:1',
        'supports' => 'sometimes|array|min:0',
        'supports.*' => 'sometimes|string|min:0',
        'current_version' => 'sometimes|string|min:1|nullable'
    ];

    public static array $update_validation_rules = [
        'status' => 'sometimes|integer|between:0,4',
        'repository_id' => 'sometimes|nullable|exists:composer_repositories,id',
        'target_version' => 'sometimes|string|min:1',
        'supports' => 'sometimes|array|min:0',
        'supports.*' => 'sometimes|string|min:0',
        'current_version' => 'sometimes|string|min:1|nullable'
    ];

    public function __construct(
        protected ThemeRepository $themeRepository
    )
    {
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function createTheme(array $attributes): Model
    {
        $validator = Validator::make($attributes, self::$create_validation_rules);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        return $this->themeRepository->create($attributes);
    }

    /**
     * @param bool $deleteFiles
     * @throws FileNotFoundException
     */
    public function deleteTheme(Theme $theme, bool $deleteFiles = false)
    {
        $theme_name = $this->themeRepository->getName($theme);

        $this->themeRepository->delete($theme);

        if ($deleteFiles) {
            $files_dir = Paths::themePath($theme_name);
            if (!Storage::deleteDirectory($files_dir)) {
                throw new FileNotFoundException('Directory "' . $files_dir . '" could not be deleted');
            }
        }
    }

    public function find(int|string $id): Model|null
    {
        return $this->themeRepository->find($id);
    }

    public function findByName(string $name): Model|null
    {
        return $this->themeRepository->findByName($name);
    }

    public function getComposerRepository(Theme $theme): Model
    {
        return $this->themeRepository->getComposerRepository($theme);
    }

    public function setComposerRepository(Theme $theme, ComposerRepository $composerRepository)
    {
        $this->themeRepository->setComposerRepository($theme, $composerRepository);
    }

    public function updateTheme(Theme $theme, array $attributes)
    {
        $validator = Validator::make($attributes, self::$update_validation_rules);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        $this->themeRepository->update($theme, $attributes);
    }
}