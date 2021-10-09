<?php


namespace Latus\Plugins\Composer;


use Composer\Console\Application;
use Illuminate\Console\BufferedConsoleOutput;
use Latus\Helpers\Paths;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;

class CLInterface
{

    protected static Application $composer;
    protected array $globalArguments = [];
    protected string $currentWorkingDir;

    public function __construct()
    {
        $this->setWorkingDir(Paths::basePath());
    }

    public function setIsQuiet(bool $isQuiet)
    {
        if ($isQuiet) {
            if (!isset($this->globalArguments['--quiet'])) {
                $this->globalArguments['--quiet'] = true;
            }
            return;
        }

        if (isset($this->globalArguments['--quiet'])) {
            unset($this->globalArguments['--quiet']);
        }
    }

    public function arguments(array $arguments = []): array
    {
        return array_merge_recursive($arguments, $this->globalArguments, ['--working-dir' => str_replace('\\', '/', $this->getWorkingDir())]);
    }

    public function getComposer(): Application
    {
        if (!isset($this->{'composer'})) {
            self::$composer = new Application();
            self::$composer->setCatchExceptions(true);
            self::$composer->setAutoExit(false);
        }

        return self::$composer;
    }

    protected function runCommand(string $command, array $arguments): CommandResult
    {
        if (!defined('LATUS_COMPOSER')) {
            define('LATUS_COMPOSER', true);
        }

        $arguments = array_merge_recursive(['command' => $command], $arguments);
        $input = new ArrayInput($arguments);

        $output = new BufferedConsoleOutput();

        try {
            $code = $this->getComposer()->run($input, $output);
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

    public function requirePackage(string $package, string $version, bool $install = true): CommandResult
    {
        $arguments = $this->arguments([
            'packages' => [
                $package . ':' . $version
            ]
        ]);

        if (!$install) {
            $arguments['--no-update'] = true;
        }

        return $this->runCommand('require', $arguments);
    }

    public function removePackage(string $package, bool $install = true): CommandResult
    {
        $arguments = $this->arguments([
            'packages' => [
                $package
            ]
        ]);

        if (!$install) {
            $arguments['--no-update'] = true;
        }

        return $this->runCommand('remove', $arguments);
    }

    public function updatePackage(string $package, string $version = null): CommandResult
    {

        $package = ($version ? '"' . $package . ':' . $version . '"' : '"' . $package . '"');

        return $this->runCommand('update', $this->arguments([
            'packages' => [
                $package . ':' . $version
            ]
        ]));
    }

    public function addRepository(string $name, string $type, string $url, bool $symlink = false): CommandResult
    {

        $options = '\'{"type": "' . $type . '", "url": "' . $url . '"' . ($symlink ? ', "options": {"symlink": true}' : '') . '}\'';

        return $this->runCommand('config', $this->arguments([
            'repositories.' . $name,
            $options
        ]));
    }

    public function removeRepository(string $name): CommandResult
    {
        return $this->runCommand('config', $this->arguments([
            '--unset' => true,
            'repositories.' . $name
        ]));
    }

    public function update(): CommandResult
    {
        return $this->runCommand('update', $this->arguments());
    }

    public function install(): CommandResult
    {
        return $this->runCommand('install', $this->arguments());
    }

}