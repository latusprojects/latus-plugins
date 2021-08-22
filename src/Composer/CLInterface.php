<?php


namespace Latus\Plugins\Composer;


use Composer\Console\Application;
use Illuminate\Console\BufferedConsoleOutput;
use Latus\Helpers\Paths;
use Symfony\Component\Console\Input\StringInput;

class CLInterface
{

    protected Application $composer;
    protected string $currentWorkingDir;

    public function __construct()
    {
        $this->setWorkingDir(Paths::basePath());

        $this->composer = new Application();
        $this->composer->setCatchExceptions(true);
        $this->composer->setAutoExit(false);
    }

    protected function runCommand(string $command, array $arguments): CommandResult
    {
        $input = new StringInput($command . ' ' . implode(' ', $arguments));

        $output = new BufferedConsoleOutput();

        try {
            $code = $this->composer->run($input, $output);
        } catch (\Exception $e) {
            return new CommandResult(CommandResult::CODE_EXECUTION_ERROR, 'There was an error executing composer. This may be due to missing or invalid permissions on system-level.');
        }

        $result = $output->fetch();

        return new CommandResult($code, $result);
    }

    public function getWorkingDir(): string
    {
        return $this->currentWorkingDir;
    }

    public function setWorkingDir(string $dir)
    {
        $this->currentWorkingDir = $dir;
    }

    public function addPackage(string $package, string $version): CommandResult
    {
        return $this->runCommand('require', [
            '"' . $package . ':' . $version . '"',
            '--working-dir="' . str_replace('\\', '/', $this->getWorkingDir()) . '"'
        ]);
    }

    public function removePackage(string $package): CommandResult
    {
        return $this->runCommand('remove', [
            $package,
            '--working-dir="' . str_replace('\\', '/', $this->getWorkingDir()) . '"'
        ]);
    }

    public function updatePackage(string $package, string $version): CommandResult
    {
        return $this->runCommand('update', [
            '"' . $package . ':' . $version . '"',
            '--working-dir="' . str_replace('\\', '/', $this->getWorkingDir()) . '"'
        ]);
    }

    public function addRepository(string $name, string $type, string $url, bool $symlink = false): CommandResult
    {

        $options = '\'{"type": "' . $type . '", "url": "' . $url . '", "options": {"symlink": ' . $symlink . '}}\'';

        return $this->runCommand('config', [
            'repositories.' . $name,
            $options
        ]);
    }

    public function removeRepository(string $name): CommandResult
    {
        return $this->runCommand('config', [
            '--unset',
            'repositories.' . $name,
            '--working-dir="' . str_replace('\\', '/', $this->getWorkingDir()) . '"'
        ]);
    }

    public function update(): CommandResult
    {
        return $this->runCommand('update', [
            '--working-dir="' . str_replace('\\', '/', $this->getWorkingDir()) . '"'
        ]);
    }

    public function install(): CommandResult
    {
        return $this->runCommand('install', [
            '--working-dir="' . str_replace('\\', '/', $this->getWorkingDir()) . '"'
        ]);
    }

}