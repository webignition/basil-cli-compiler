<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

use webignition\BasilModels\Test\TestInterface;

class CompiledTest
{
    private TestInterface $test;
    private string $code;
    private string $className;

    public function __construct(TestInterface $test, string $code, string $className)
    {
        $this->test = $test;
        $this->code = $code;
        $this->className = $className;
    }

    public function getTest(): TestInterface
    {
        return $this->test;
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
