<?php


namespace Latus\Plugins\Composer;


use Latus\Helpers\Paths;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Plugins\Models\Plugin;
use Latus\Plugins\Models\Theme;

class ProxyPackage
{

    public const PACKAGE_TYPE_PLUGIN = Plugin::class;
    public const PACKAGE_TYPE_THEME = Theme::class;

    public const PREFIX = 'latus-package-';
    public const IGNORED_DEPENDENCIES = [
        'laravel/framework',
        'latusprojects/latus'
    ];

    public function __construct(
        protected ComposerRepository $composerRepository,
        protected Plugin|Theme $model,
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

    public function getActualName(): string
    {
        return $this->model->name;
    }

    public function getName(): string
    {
        return self::PREFIX . $this->getActualName();
    }

    public function getPackageType(): string
    {
        return get_class($this->getPackageModel());
    }

    public function getInstallDir(): string
    {
        return match ($this->getPackageType()) {
            self::PACKAGE_TYPE_PLUGIN => Paths::pluginPath($this->getName()),
            self::PACKAGE_TYPE_THEME => Paths::themePath($this->getName())
        };
    }

}