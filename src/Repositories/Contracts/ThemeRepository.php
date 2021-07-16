<?php


namespace Latus\Plugins\Repositories\Contracts;


use Illuminate\Database\Eloquent\Model;
use Latus\Plugins\Models\Theme;
use Latus\Repositories\Contracts\Repository;

interface ThemeRepository extends Repository
{

    public function __construct(Theme $theme);

    public function setAsActiveTheme(string $component, Theme $theme);

    public function delete(Theme $theme);

    public function getName(Theme $theme): string;

    public function findByName(string $name): Model|null;
}