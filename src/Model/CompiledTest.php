<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

class CompiledTest
{
    public function __construct(
        private string $code,
        private string $className
    ) {
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
