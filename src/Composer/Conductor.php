<?php


namespace Latus\Plugins\Composer;


class Conductor
{

    public function __construct(
        protected CLIInterface $CLI,
        protected ProxyPackageFileHandler $fileHandler,
    )
    {
    }

    public function installOrUpdatePackage(ProxyPackage $proxyPackage)
    {

        $this->fileHandler->setPackage($proxyPackage);
        $this->fileHandler->buildFile();
        

    }
}