<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

class CompiledTest
{
    private string $code;
    private string $className;
    private string $testPath;

    public function __construct(string $code, string $className, string $testPath)
    {
        $this->code = $code;
        $this->className = $className;
        $this->testPath = $testPath;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getTestPath(): string
    {
        return $this->testPath;
    }
}
