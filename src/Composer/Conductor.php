<?php


namespace Latus\Plugins\Composer;


use Illuminate\Support\Facades\File;
use Latus\Helpers\Paths;
use Latus\Plugins\Exceptions\ComposerCLIException;

class Conductor
{

    protected Package $proxyPackage;
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
        $this->CLI->setWorkingDir(Paths::basePath());

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
    public function removePackage(Package $proxyPackage)
    {
        $this->ensureMetaComposerRepositoriesExist();

        $this->proxyPackage = $proxyPackage;

        $this->CLI->setWorkingDir(Paths::basePath());

        $removePackageResult = $this->CLI->removePackage($proxyPackage->getName());
        $removeRepositoryResult = $this->CLI->removeRepository($proxyPackage->getName());

        if ($this->hadFailure && !$this->filesRemoved) {
            $this->filesRemoved = true;
        }

        $this->failIfResultHasErrors($removePackageResult);
        $this->failIfResultHasErrors($removeRepositoryResult);


        $this->fileHandler->setPackage($proxyPackage);
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
    public function installOrUpdatePackage(Package $proxyPackage)
    {
        $this->ensureMetaComposerRepositoriesExist();

        $this->proxyPackage = $proxyPackage;

        if ($proxyPackage->getRepository()->type !== 'path') {
            $this->fileHandler->setPackage($proxyPackage);
            $this->fileHandler->buildFile();
        }

        $this->CLI->setWorkingDir($proxyPackage->getInstallDir());

        $result = null;

        if (!File::exists($proxyPackage->getInstallDir() . DIRECTORY_SEPARATOR . 'composer.lock')) {
            $result = $this->CLI->install();
        } else {
            $result = $this->CLI->update();
        }

        $this->failIfResultHasErrors($result);

        $this->CLI->setWorkingDir(Paths::basePath());

        $addRepositoryResult = $this->CLI->addRepository(
            $proxyPackage->getName(),
            'path',
            str_replace(DIRECTORY_SEPARATOR, '/', $proxyPackage->getRelativeInstallDir())
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
                $this->removePackage($this->proxyPackage);
            }
            $exception = new ComposerCLIException();
            $exception->setCommandResult($commandResult);
            throw $exception;
        }
    }
}