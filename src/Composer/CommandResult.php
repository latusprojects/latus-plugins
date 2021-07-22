<?php


namespace Latus\Plugins\Composer;


class CommandResult
{
    public const CODE_OK = 0;
    public const CODE_UNKNOWN_ERROR = 1;
    public const CODE_DEPENDENCY_ERROR = 2;

    public function __construct(
        protected int $code,
        protected string $result
    )
    {
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getResult(): string
    {
        return $this->result;
    }
}