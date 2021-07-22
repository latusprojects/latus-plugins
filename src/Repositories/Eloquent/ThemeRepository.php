<?php


namespace Latus\Plugins\Repositories\Eloquent;


use Illuminate\Database\Eloquent\Model;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Plugins\Models\Theme;
use Latus\Plugins\Repositories\Contracts\ThemeRepository as ThemeRepositoryContract;
use Latus\Repositories\EloquentRepository;

class ThemeRepository extends EloquentRepository implements ThemeRepositoryContract
{

    public function __construct(Theme $theme)
    {
        parent::__construct($theme);
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

    public function setAsActiveTheme(string $component, Theme $theme)
    {

    }

    public function getComposerRepository(Theme $theme): Model
    {
        return $theme->repository()->first();
    }

    public function setComposerRepository(Theme $theme, ComposerRepository $composerRepository)
    {
        $theme->repository()->associate($composerRepository);
    }
}