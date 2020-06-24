<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\BasilCliCompiler\Model\GeneratedTestOutput;
use webignition\BasilCompilableSourceFactory\Exception\UnsupportedStepException;
use webignition\BasilCompiler\Compiler;
use webignition\BasilCompiler\UnresolvedPlaceholderException;
use webignition\BasilModels\Test\TestInterface;

class TestWriter
{
    private Compiler $compiler;
    private PhpFileCreator $phpFileCreator;

    public function __construct(Compiler $compiler, PhpFileCreator $phpFileCreator)
    {
        $this->compiler = $compiler;
        $this->phpFileCreator = $phpFileCreator;
    }

    /**
     * @param TestInterface $test
     * @param string $fullyQualifiedBaseClass
     * @param string $outputDirectory
     *
     * @return GeneratedTestOutput
     *
     * @throws UnresolvedPlaceholderException
     * @throws UnsupportedStepException
     */
    public function generate(
        TestInterface $test,
        string $fullyQualifiedBaseClass,
        string $outputDirectory
    ): GeneratedTestOutput {
        $className = $this->compiler->createClassName($test);
        $code = $this->compiler->compile($test, $fullyQualifiedBaseClass);

        $this->phpFileCreator->setOutputDirectory($outputDirectory);
        $filename = $this->phpFileCreator->create($className, $code);

        return new GeneratedTestOutput($test->getPath() ?? '', $filename);
    }
}
