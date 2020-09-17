<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

class CompiledTest
{
    private string $code;
    private string $className;

    public function __construct(string $code, string $className)
    {
        $this->code = $code;
        $this->className = $className;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getClassName(): string
    {
        return $this->className;
    }
}
