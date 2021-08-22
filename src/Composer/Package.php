<?php


namespace Latus\Plugins\Composer;


use Latus\Helpers\Paths;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Plugins\Models\Plugin;
use Latus\Plugins\Models\Theme;

class Package
{

    public const PACKAGE_TYPE_PLUGIN = Plugin::class;
    public const PACKAGE_TYPE_THEME = Theme::class;

    public const NAME_TYPE_FULL = 'vendor-package';
    public const NAME_TYPE_VENDOR = 'vendor';
    public const NAME_TYPE_PACKAGE = 'package';

    public const IGNORED_DEPENDENCIES = [
        'laravel/framework',
        'latusprojects/latus',
        'latusprojects/latus-collections',
        'latusprojects/latus-composer-plugins',
        'latusprojects/latus-plugins',
        'latusprojects/latus-content',
        'latusprojects/latus-helpers',
        'latusprojects/latus-installer',
        'latusprojects/latus-model-repositories',
        'latusprojects/latus-permissions',
        'latusprojects/latus-prioritized-providers',
        'latusprojects/latus-settings',
        'latusprojects/latus-ui'
    ];

    public function __construct(
        protected ComposerRepository $composerRepository,
        protected Plugin|Theme       $model,
    )
    {
    }

    public function getRepository(): ComposerRepository
    {
        return $this->composerRepository;
    }

    public function getPackageModel(): Plugin|Theme
    {
        return $this->model;
    }

    public function getName(string $type = self::NAME_TYPE_FULL): string
    {
        $fullName = $this->getPackageModel()->name;

        return match ($type) {
            self::NAME_TYPE_FULL => $fullName,
            self::NAME_TYPE_VENDOR => explode('/', $fullName)[0],
            self::NAME_TYPE_PACKAGE => explode('/', $fullName)[1]
        };
    }

    public function getPackageType(): string
    {
        return get_class($this->getPackageModel());
    }

    protected function getDirPrefix(bool $absolute = true): string
    {
        if ($absolute) {
            return match ($this->getPackageType()) {
                self::PACKAGE_TYPE_PLUGIN => Paths::pluginPath(),
                self::PACKAGE_TYPE_THEME => Paths::themePath()
            };
        }

        return match ($this->getPackageType()) {
            self::PACKAGE_TYPE_PLUGIN => 'plugins' . DIRECTORY_SEPARATOR,
            self::PACKAGE_TYPE_THEME => 'themes' . DIRECTORY_SEPARATOR
        };
    }

    public function getInstallDir(bool $absolute = true): string
    {
        return $this->getDirPrefix($absolute) . $this->getName(self::NAME_TYPE_VENDOR) . DIRECTORY_SEPARATOR . $this->getName(self::NAME_TYPE_PACKAGE);
    }

}