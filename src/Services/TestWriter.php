<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\BasilCliCompiler\Model\GeneratedTestOutput;
use webignition\BasilCompilableSource\ClassDefinition;
use webignition\BasilCompilableSource\Expression\ClassDependency;
use webignition\BasilCompilableSourceFactory\ClassDefinitionFactory;
use webignition\BasilCompilableSourceFactory\Exception\UnsupportedStepException;
use webignition\BasilCompiler\Compiler;
use webignition\BasilCompiler\UnresolvedPlaceholderException;
use webignition\BasilModels\Test\TestInterface;

class TestWriter
{
    private Compiler $compiler;
    private PhpFileCreator $phpFileCreator;
    private ClassDefinitionFactory $classDefinitionFactory;

    public function __construct(
        Compiler $compiler,
        PhpFileCreator $phpFileCreator,
        ClassDefinitionFactory $classDefinitionFactory
    ) {
        $this->compiler = $compiler;
        $this->phpFileCreator = $phpFileCreator;
        $this->classDefinitionFactory = $classDefinitionFactory;
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
        $classDefinition = $this->classDefinitionFactory->createClassDefinition($test);
        if ($classDefinition instanceof ClassDefinition) {
            $classDefinition->setBaseClass(new ClassDependency($fullyQualifiedBaseClass));
        }

        $code = $this->compiler->compile($classDefinition);

        $this->phpFileCreator->setOutputDirectory($outputDirectory);
        $filename = $this->phpFileCreator->create($classDefinition->getName(), $code);

        return new GeneratedTestOutput($test->getPath() ?? '', $filename);
    }
}
