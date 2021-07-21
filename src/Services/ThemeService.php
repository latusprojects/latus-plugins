<?php


namespace Latus\Plugins\Services;


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
        'supports' => 'required|array|min:1',
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
}