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

    public function getActualName(bool $formatted = false): string
    {
        return $formatted ? str_replace('/', '%%', $this->model->name) : $this->model->name;
    }

    public function getName(bool $formatted = false): string
    {
        $name = '';

        if ($this->getRepository()->type === 'path') {
            $name = $this->getActualName();
        } else {
            $name = self::PREFIX . $this->getActualName();
        }

        return $formatted ? str_replace('/', '%%', $name) : $name;
    }

    public function getPackageType(): string
    {
        return get_class($this->getPackageModel());
    }

    public function getRelativeInstallDir(): string
    {
        if ($this->getPackageType() === self::PACKAGE_TYPE_PLUGIN) {
            if ($this->getRepository()->type === 'path') {
                return 'plugins' . DIRECTORY_SEPARATOR . 'local' . DIRECTORY_SEPARATOR . $this->getName(true);
            }
            return 'plugins' . DIRECTORY_SEPARATOR . $this->getName(true);
        } else {
            if ($this->getRepository()->type === 'path') {

                return 'themes' . DIRECTORY_SEPARATOR . 'local' . DIRECTORY_SEPARATOR . $this->getName(true);
            }
            return 'themes' . DIRECTORY_SEPARATOR . $this->getName(true);
        }
    }

    public function getInstallDir(): string
    {

        if ($this->getPackageType() === self::PACKAGE_TYPE_PLUGIN) {
            if ($this->getRepository()->type === 'path') {
                return Paths::pluginPath('local' . DIRECTORY_SEPARATOR . $this->getName(true));
            }
            return Paths::pluginPath($this->getName(true));
        } else {
            if ($this->getRepository()->type === 'path') {

                return Paths::themePath('local' . DIRECTORY_SEPARATOR . $this->getName(true));
            }
            return Paths::themePath($this->getName(true));
        }


    }

}