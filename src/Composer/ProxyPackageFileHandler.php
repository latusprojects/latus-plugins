<?php


namespace Latus\Plugins\Composer;


use Illuminate\Support\Facades\File;

class ProxyPackageFileHandler
{

    protected ProxyPackage $proxyPackage;

    public function setPackage(ProxyPackage $proxyPackage)
    {
        $this->proxyPackage = $proxyPackage;
    }

    public function deleteFiles()
    {
        File::deleteDirectory($this->proxyPackage->getInstallDir());
    }

    public function updateVersion()
    {
        $data = json_decode($this->getFileContents());

        $data->require = json_decode('{}');

        $data->require->{$this->proxyPackage->getActualName()} = $this->proxyPackage->getPackageModel()->target_version;

        $this->putFileContents(json_encode($data));
    }

    public function updateRepository()
    {
        $data = json_decode($this->getFileContents());

        $repository = $this->proxyPackage->getRepository();
        $data->repositories->{$repository->name}->{'type'} = $repository->type;
        $data->repositories->{$repository->name}->{'url'} = $repository->url;

        $this->putFileContents(json_encode($data));
    }

    protected function getFileContents(): string
    {
        return File::get($this->proxyPackage->getInstallDir() . DIRECTORY_SEPARATOR . 'composer.json');
    }

    protected function putFileContents(string $contents)
    {
        File::put($this->proxyPackage->getInstallDir() . DIRECTORY_SEPARATOR . 'composer.json', $contents);
    }

    public function buildFile()
    {
        $data = json_decode('{}');
        $data->name = $this->proxyPackage->getName();
        $data->type = 'latus-proxy-' . ($this->proxyPackage->getPackageType() === ProxyPackage::PACKAGE_TYPE_PLUGIN ? 'plugin' : 'theme');
        $data->version = $this->proxyPackage->getPackageModel()->target_version;
        $data->require = json_decode('{}');
        $data->replace = json_decode('{}');
        $data->repositories = json_decode('{}');

        $repository = $this->proxyPackage->getRepository();

        $data->require->{$this->proxyPackage->getActualName()} = $this->proxyPackage->getPackageModel()->target_version;

        $data->repositories->{$repository->name} = json_decode('{}');
        $data->repositories->{$repository->name}->{'type'} = $repository->type;
        $data->repositories->{$repository->name}->{'url'} = $repository->url;

        foreach (ProxyPackage::IGNORED_DEPENDENCIES as $IGNORED_DEPENDENCY) {
            $data->replace->{$IGNORED_DEPENDENCY} = '*';
        }

        $contents = json_encode($data);

        File::makeDirectory($this->proxyPackage->getInstallDir(), 0755, true);

        $this->putFileContents($contents);
    }
}