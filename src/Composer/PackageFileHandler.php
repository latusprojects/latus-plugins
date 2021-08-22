<?php


namespace Latus\Plugins\Composer;


use Illuminate\Support\Facades\File;
use Latus\Helpers\Paths;

class PackageFileHandler
{

    protected Package $proxyPackage;

    public function __construct()
    {
        $this->ensureMetaComposerPackagesExist();
    }

    protected function ensureMetaComposerPackagesExist()
    {
        File::ensureDirectoryExists(Paths::pluginPath());
        File::ensureDirectoryExists(Paths::themePath());

        $fileContentArray = [
            'name' => '',
            'type' => 'meta',
            'version' => '1.0',
            'require' => []
        ];

        if (!File::exists(Paths::pluginPath('composer.json'))) {
            File::put(Paths::pluginPath('composer.json'), json_encode($fileContentArray['name'] = 'latus-packages/plugins'));
        }

        if (!File::exists(Paths::themePath('composer.json'))) {
            File::put(Paths::themePath('composer.json'), json_encode($fileContentArray['name'] = 'latus-packages/themes'));
        }

    }

    public function setPackage(Package $proxyPackage)
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

}