<?php


namespace Latus\Plugins\Repositories\Contracts;


use Illuminate\Database\Eloquent\Model;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Plugins\Models\Theme;
use Latus\Repositories\Contracts\Repository;
use Latus\UI\Components\Contracts\ModuleComponent;

interface ThemeRepository extends Repository
{

    public function __construct(Theme $theme);

    public function setThemeForModule(Theme $theme, ModuleComponent $moduleComponent);

    public function delete(Theme $theme);

    public function getName(Theme $theme): string;

    public function findByName(string $name): Model|null;

    public function getComposerRepository(Theme $theme): Model;

    public function setComposerRepository(Theme $theme, ComposerRepository $composerRepository);
}