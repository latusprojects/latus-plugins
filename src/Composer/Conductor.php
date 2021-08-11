<?php


namespace Latus\Plugins\Composer;


use Composer\InstalledVersions;
use Illuminate\Support\Facades\File;
use Latus\Helpers\Paths;
use Latus\Plugins\Exceptions\ComposerCLIException;

class Conductor
{

    protected ProxyPackage $proxyPackage;
    protected bool $filesRemoved = false;
    protected bool $hadFailure = false;

    public function __construct(
        protected CLInterface             $CLI,
        protected ProxyPackageFileHandler $fileHandler,
    )
    {
    }

    /**
     * @throws ComposerCLIException
     */
    public function removePackage(ProxyPackage $proxyPackage)
    {

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
        $this->CLI->setWorkingDir(Paths::basePath());

        $removeRepositoryResult = $this->CLI->removeRepository($repositoryName);

        $this->failIfResultHasErrors($removeRepositoryResult);
    }

    /**
     * @throws ComposerCLIException
     */
    public function installOrUpdatePackage(ProxyPackage $proxyPackage)
    {
        $this->proxyPackage = $proxyPackage;

        if ($proxyPackage->getRepository()->type !== 'path') {
            $this->fileHandler->setPackage($proxyPackage);
            $this->fileHandler->buildFile();
        }

        $this->CLI->setWorkingDir($proxyPackage->getInstallDir());

        $result = null;

        if (!InstalledVersions::isInstalled($proxyPackage->getActualName())) {
            $result = $this->CLI->installPackage($proxyPackage->getActualName(), $proxyPackage->getPackageModel()->target_version);
        } else {
            $result = $this->CLI->updatePackage($proxyPackage->getActualName(), $proxyPackage->getPackageModel()->target_version);
        }

        $this->failIfResultHasErrors($result);

        $this->CLI->setWorkingDir(Paths::basePath());

        $addRepositoryResult = $this->CLI->addRepository(
            $proxyPackage->getName(),
            'path',
            $proxyPackage->getRelativeInstallDir(),
            true
        );

        $this->failIfResultHasErrors($addRepositoryResult);
    }

    public function fetchLocalPackageInfo(string $packageType, string $packageName): array|null
    {

        $packagePathPrefix = match ($packageType) {
            ProxyPackage::PACKAGE_TYPE_THEME => 'themes' . DIRECTORY_SEPARATOR . 'local' . DIRECTORY_SEPARATOR,
            ProxyPackage::PACKAGE_TYPE_PLUGIN => 'plugins' . DIRECTORY_SEPARATOR . 'local' . DIRECTORY_SEPARATOR,
        };

        $packageComposerPath = $packagePathPrefix . $packageName . DIRECTORY_SEPARATOR . 'composer.json';

        if (!file_exists($packageComposerPath) || !($composerContent = json_decode(File::get($packageComposerPath)))) {
            return null;
        }

        return [
            'name' => $composerContent->{'name'},
            'version' => $composerContent->{'version'},
            'author' => $composerContent->{'author'},
            'authors' => (array)$composerContent->{'authors'},
            'description' => $composerContent->{'description'},
            'repositories' => (array)$composerContent->{'repositories'},
        ];

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