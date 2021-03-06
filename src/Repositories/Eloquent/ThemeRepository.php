<?php


namespace Latus\Plugins\Repositories\Eloquent;


use Illuminate\Database\Eloquent\Model;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Plugins\Models\Theme;
use Latus\Plugins\Repositories\Contracts\ThemeRepository as ThemeRepositoryContract;
use Latus\Repositories\EloquentRepository;
use Latus\Settings\Models\Setting;
use Latus\Settings\Services\SettingService;

class ThemeRepository extends EloquentRepository implements ThemeRepositoryContract
{

    public function __construct(
        protected SettingService $settingService
    )
    {
    }

    public function relatedModel(): Model
    {
        return new Theme();
    }

    public function delete(Theme $theme)
    {
        $theme->delete();
    }

    public function getName(Theme $theme): string
    {
        return $theme->name;
    }

    public function findByName(string $name): Model|null
    {
        return Theme::where('name', $name)->first();
    }

    public function update(Theme $theme, array $attributes)
    {
        $theme->update($attributes);
    }

    public function getComposerRepository(Theme $theme): Model
    {
        return $theme->repository()->first();
    }

    public function setComposerRepository(Theme $theme, ComposerRepository $composerRepository)
    {
        $theme->repository()->associate($composerRepository);
    }

    public function setAsActiveThemeForModule(Theme $theme, string $moduleContract): bool
    {
        if (!isset($theme->supports[$moduleContract])) {
            return false;
        }

        /**
         * @var Setting $setting
         */
        $activeModules = json_decode($this->settingService->findByKey('active_modules'), true);

        $activeModules[$moduleContract] = $theme->supports[$moduleContract];

        $this->settingService->setSettingValue($setting, $activeModules);

        return true;
    }
}