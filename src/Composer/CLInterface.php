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

    /**
     * @throws \Exception
     */
    protected function runCommand(string $command, array $arguments): CommandResult
    {
        $input = new StringInput($command . ' ' . implode(' ', $arguments));

        $output = new BufferedConsoleOutput();

        $code = $this->composer->run($input, $output);

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

    /**
     * @throws \Exception
     */
    public function addPackage(string $package, string $version): CommandResult
    {
        return $this->runCommand('require', [
            '"' . $package . ':' . $version . '"',
            '--working-dir=' . $this->getWorkingDir()
        ]);
    }

    /**
     * @throws \Exception
     */
    public function removePackage(string $package): CommandResult
    {
        return $this->runCommand('remove', [
            $package,
            '--working-dir=' . $this->getWorkingDir()
        ]);
    }

    /**
     * @throws \Exception
     */
    public function updatePackage(string $package, string $version): CommandResult
    {
        return $this->runCommand('update', [
            '"' . $package . ':' . $version . '"',
            '--working-dir=' . $this->getWorkingDir()
        ]);
    }

    /**
     * @throws \Exception
     */
    public function addRepository(string $name, string $type, string $url, bool $symlink = true): CommandResult
    {
        $options = json_encode([
            'type' => $type,
            'url' => $url,
            'symlink' => $symlink
        ]);

        return $this->runCommand('config', [
            'repositories.' . $name,
            $options,
            '--working-dir=' . $this->getWorkingDir()
        ]);
    }

    /**
     * @throws \Exception
     */
    public function removeRepository(string $name): CommandResult
    {
        return $this->runCommand('config', [
            '--unset',
            'repositories.' . $name,
            '--working-dir=' . $this->getWorkingDir()
        ]);
    }

    /**
     * @throws \Exception
     */
    public function update(): CommandResult
    {
        return $this->runCommand('update', [
            '--working-dir=' . $this->getWorkingDir()
        ]);
    }

    /**
     * @throws \Exception
     */
    public function install(): CommandResult
    {
        return $this->runCommand('install', [
            '--working-dir=' . $this->getWorkingDir()
        ]);
    }

}