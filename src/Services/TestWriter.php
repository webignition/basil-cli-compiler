<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\BasilCliCompiler\Model\CompiledTest;

class TestWriter
{
    private PhpFileCreator $phpFileCreator;

    public function __construct(PhpFileCreator $phpFileCreator)
    {
        $this->phpFileCreator = $phpFileCreator;
    }

    public static function createWriter(): self
    {
        return new TestWriter(
            new PhpFileCreator()
        );
    }

    public function write(CompiledTest $compiledTest, string $outputDirectory): string
    {
        $this->phpFileCreator->setOutputDirectory($outputDirectory);
        $filename = $this->phpFileCreator->create($compiledTest->getClassName(), $compiledTest->getCode());

        return $outputDirectory . '/' . $filename;
    }
}
