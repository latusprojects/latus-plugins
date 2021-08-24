<?php


namespace Latus\Plugins\Composer;


use Illuminate\Support\Facades\File;
use Latus\Helpers\Paths;

class PackageFileHandler
{

    protected Package $package;

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
            'type' => 'metapackage',
            'version' => '1.0.0',
            'require' => json_decode('{}')
        ];

        if (!File::exists(Paths::pluginPath('composer.json'))) {
            $fileContentArray['name'] = 'latus-packages/plugins';
            File::put(Paths::pluginPath('composer.json'), json_encode($fileContentArray));
        }

        if (!File::exists(Paths::themePath('composer.json'))) {
            $fileContentArray['name'] = 'latus-packages/themes';
            File::put(Paths::themePath('composer.json'), json_encode($fileContentArray));
        }

    }

    public function setPackage(Package $package)
    {
        $this->package = $package;
    }

    public function deleteFiles()
    {
        File::deleteDirectory($this->package->getInstallDir());
    }

    public function unRequire()
    {
        $data = json_decode($this->getFileContents());

        $data->require = json_decode('{}');

        unset($data->require->{$this->package->getName()});

        $this->putFileContents(json_encode($data));
    }

    public function updateVersion()
    {
        $data = json_decode($this->getFileContents());

        $data->require = json_decode('{}');

        $data->require->{$this->package->getName()} = $this->package->getPackageModel()->target_version;

        $this->putFileContents(json_encode($data));
    }

    protected function getFileContents(): string
    {
        return File::get($this->package->getMetaPackageDir() . 'composer.json');
    }

    protected function putFileContents(string $contents)
    {
        File::put($this->package->getMetaPackageDir() . 'composer.json', $contents);
    }

}