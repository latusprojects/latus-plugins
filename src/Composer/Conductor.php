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

        $addRepositoryResult = $this->CLI->addRepository(
            'latus-packages/plugins',
            'path',
            str_replace(DIRECTORY_SEPARATOR, '/', Paths::pluginPath())
        );

        $this->failIfResultHasErrors($addRepositoryResult);

        $addRepositoryResult = $this->CLI->addRepository(
            'latus-packages/themes',
            'path',
            str_replace(DIRECTORY_SEPARATOR, '/', Paths::themePath())
        );

        $this->failIfResultHasErrors($addRepositoryResult);

    }

    /**
     * @throws ComposerCLIException
     */
    protected function ensurePathComposerRepositoryExists()
    {

        $url = $this->package->getInstallDir(false);

        $addRepositoryResult = $this->CLI->addRepository(
            $this->package->getName(),
            'path',
            str_replace(DIRECTORY_SEPARATOR, '/', $url),
            true
        );

        $this->failIfResultHasErrors($addRepositoryResult);
    }

    /**
     * @throws ComposerCLIException
     */
    public function removePackage(Package $package)
    {
        $this->CLI->setWorkingDir(Paths::basePath());

        $this->ensureMetaComposerRepositoriesExist();

        $this->package = $package;

        $removePackageResult = $this->CLI->removePackage($package->getName());
        $removeRepositoryResult = $this->CLI->removeRepository($package->getName());

        if ($this->hadFailure && !$this->filesRemoved) {
            $this->filesRemoved = true;
        }

        $this->failIfResultHasErrors($removePackageResult);
        $this->failIfResultHasErrors($removeRepositoryResult);


        $this->fileHandler->setPackage($package);
        $this->fileHandler->deleteFiles();


    }

    /**
     * @throws ComposerCLIException
     */
    public function removeRepository(string $repositoryName)
    {
        $this->ensureMetaComposerRepositoriesExist();

        $this->CLI->setWorkingDir(Paths::basePath());

        $removeRepositoryResult = $this->CLI->removeRepository($repositoryName);

        $this->failIfResultHasErrors($removeRepositoryResult);
    }

    /**
     * @throws ComposerCLIException
     */
    public function installOrUpdatePackage(Package $package)
    {
        $this->ensureMetaComposerRepositoriesExist();

        $this->package = $package;

        $this->CLI->setWorkingDir(Paths::basePath());

        if ($package->getRepository()->type === 'path') {
            $this->ensurePathComposerRepositoryExists();
        }

        $this->CLI->setWorkingDir($package->getInstallDir());

        $result = null;

        if (!File::exists($package->getInstallDir() . DIRECTORY_SEPARATOR . 'composer.lock')) {
            $result = $this->CLI->install();
        } else {
            $result = $this->CLI->update();
        }

        $this->failIfResultHasErrors($result);

        $this->CLI->setWorkingDir(Paths::basePath());

        $addRepositoryResult = $this->CLI->addRepository(
            $package->getName(),
            'path',
            str_replace(DIRECTORY_SEPARATOR, '/', $package->getInstallDir(false))
        );

        $this->failIfResultHasErrors($addRepositoryResult);
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