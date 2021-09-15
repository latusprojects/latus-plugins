<?php


namespace Latus\Plugins\Composer;


use Illuminate\Support\Facades\File;
use Latus\Helpers\Paths;
use Latus\Plugins\Exceptions\ComposerCLIException;

class Conductor
{

    protected Package $package;
    protected bool $filesRemoved = false;
    protected bool $hadFailure = false;

    public function __construct(
        protected CLInterface        $CLI,
        protected PackageFileHandler $fileHandler,
    )
    {
    }

    /**
     * @throws ComposerCLIException
     */
    protected function ensureMetaComposerRepositoriesExist()
    {

        $this->failIfResultHasErrors($this->CLI->addRepository(
            'latus-packages/plugins',
            'path',
            str_replace(DIRECTORY_SEPARATOR, '/', 'plugins'),
            true
        ));

        $this->failIfResultHasErrors($this->CLI->addRepository(
            'latus-packages/themes',
            'path',
            str_replace(DIRECTORY_SEPARATOR, '/', 'themes'),
            true
        ));

    }

    protected function ensureMetaComposerPackagesAreRequired()
    {
        $data = json_decode(File::get(Paths::basePath('composer.json')));

        $data->require->{'latus-packages/plugins'} = '1.0.0';
        $data->require->{'latus-packages/themes'} = '1.0.0';

        File::put(Paths::basePath('composer.json'), json_encode($data));
    }

    /**
     * @throws ComposerCLIException
     */
    protected function ensurePathComposerRepositoryExists()
    {

        $url = $this->package->getInstallDir(false);

        $this->failIfResultHasErrors($this->CLI->addRepository(
            $this->package->getName(),
            'path',
            str_replace(DIRECTORY_SEPARATOR, '/', $url),
            true
        ));
    }

    /**
     * @throws ComposerCLIException
     */
    public function removePackage(Package $package)
    {
        $this->CLI->setWorkingDir(Paths::basePath());

        $this->package = $package;

        $this->ensureMetaComposerRepositoriesExist();
        $this->ensureMetaComposerPackagesAreRequired();

        $this->fileHandler->setPackage($package);

        $this->fileHandler->unRequire();

        if ($package->getRepository()->type === 'path') {
            $this->failIfResultHasErrors($this->CLI->removeRepository($package->getName()));
        }

        $this->failIfResultHasErrors($this->CLI->updatePackage($package->getMetaPackageName()));
    }

    /**
     * @throws ComposerCLIException
     */
    public function installOrUpdatePackage(Package $package)
    {
        $this->package = $package;

        $this->ensureMetaComposerRepositoriesExist();
        $this->ensureMetaComposerPackagesAreRequired();

        $this->CLI->setWorkingDir(Paths::basePath());

        if ($package->getRepository()->type === 'path') {
            $this->ensurePathComposerRepositoryExists();
        }

        $this->fileHandler->setPackage($package);

        $this->fileHandler->updateVersion();

        $this->failIfResultHasErrors($this->CLI->updatePackage($package->getMetaPackageName()));
    }

    /**
     * @throws ComposerCLIException
     */
    protected function failIfResultHasErrors(CommandResult $commandResult)
    {
        if ($commandResult->getCode() !== CommandResult::CODE_OK) {
            $this->hadFailure = true;
            if (!$this->filesRemoved) {
                $this->removePackage($this->package);
            }
            $exception = new ComposerCLIException();
            $exception->setCommandResult($commandResult);
            throw $exception;
        }
    }
}