<?php


namespace Latus\Plugins\Repositories\Contracts;


use Illuminate\Database\Eloquent\Model;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Plugins\Models\Theme;
use Latus\Repositories\Contracts\Repository;
use Latus\Settings\Services\SettingService;

interface ThemeRepository extends Repository
{

    public function __construct(SettingService $settingService);

    public function setAsActiveThemeForModule(Theme $theme, string $moduleContract): bool;

    public function delete(Theme $theme);

    public function getName(Theme $theme): string;

    public function findByName(string $name): Model|null;

    public function update(Theme $theme, array $attributes);

    public function getComposerRepository(Theme $theme): Model;

    public function setComposerRepository(Theme $theme, ComposerRepository $composerRepository);
}