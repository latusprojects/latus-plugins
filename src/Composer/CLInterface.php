<?php


namespace Latus\Plugins\Composer;


use Composer\Console\Application;
use Illuminate\Console\BufferedConsoleOutput;
use Latus\Helpers\Paths;
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
            if (!in_array('--quiet', $this->globalArguments)) {
                $this->globalArguments[] = '--quiet';
            }
            return;
        }

        if (in_array('--quiet', $this->globalArguments)) {
            unset($this->globalArguments['--quiet']);
        }
    }

    public function arguments(array $arguments = []): array
    {
        return $arguments + $this->globalArguments + ['--working-dir' => '"' . str_replace('\\', '/', $this->getWorkingDir()) . '"'];
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

        $input = new StringInput($command . ' ' . implode(' ', $arguments));

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
            '"' . $package . ':' . $version . '"',
        ]);

        if (!$install) {
            $arguments[] = '--no-install';
        }

        return $this->runCommand('require', $arguments);
    }

    public function removePackage(string $package, bool $install = true): CommandResult
    {
        $arguments = $this->arguments([
            $package,
        ]);

        if (!$install) {
            $arguments[] = '--no-install';
        }

        return $this->runCommand('remove', $arguments);
    }

    public function updatePackage(string $package, string $version = null): CommandResult
    {

        $package = ($version ? '"' . $package . ':' . $version . '"' : '"' . $package . '"');

        return $this->runCommand('update', $this->arguments([
            '"' . $package . ':' . $version . '"',
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
            '--unset',
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