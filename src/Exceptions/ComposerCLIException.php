<?php


namespace Latus\Plugins\Exceptions;


use Latus\Plugins\Composer\CommandResult;

class ComposerCLIException extends \Exception
{

    protected CommandResult|null $commandResult = null;

    public function setCommandResult(CommandResult $commandResult)
    {
        $this->commandResult = $commandResult;
    }

    public function getCommandResult(): CommandResult|null
    {
        return $this->commandResult;
    }
}