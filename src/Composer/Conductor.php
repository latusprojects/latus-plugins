<?php


namespace Latus\Plugins\Composer;


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