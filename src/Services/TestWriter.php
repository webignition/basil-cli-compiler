<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\BasilCliCompiler\Model\CompiledTest;

class TestWriter
{
    public function __construct(
        private PhpFileCreator $phpFileCreator
    ) {
    }

    public static function createWriter(string $outputDirectory): self
    {
        return new TestWriter(
            new PhpFileCreator($outputDirectory)
        );
    }

    public function write(CompiledTest $compiledTest, string $outputDirectory): string
    {
        $filename = $this->phpFileCreator->create($compiledTest->getClassName(), $compiledTest->getCode());

        return $outputDirectory . '/' . $filename;
    }
}
