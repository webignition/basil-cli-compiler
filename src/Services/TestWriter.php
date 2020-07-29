<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\BasilCliCompiler\Model\CompiledTest;
use webignition\BasilCompilerModels\TestManifest;

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

    /**
     * @param CompiledTest $compiledTest
     * @param string $outputDirectory
     *
     * @return TestManifest
     *
     */
    public function write(CompiledTest $compiledTest, string $outputDirectory): TestManifest
    {
        $sourceTest = $compiledTest->getTest();
        $testPath = $sourceTest->getPath() ?? '';

        $this->phpFileCreator->setOutputDirectory($outputDirectory);
        $filename = $this->phpFileCreator->create($compiledTest->getClassName(), $compiledTest->getCode());

        return new TestManifest($sourceTest->getConfiguration(), $testPath, $filename);
    }
}
