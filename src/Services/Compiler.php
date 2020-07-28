<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\BasilCliCompiler\Exception\UnresolvedPlaceholderException;
use webignition\BasilCliCompiler\Model\CompiledTest;
use webignition\BasilCompilableSource\ClassDefinition;
use webignition\BasilCompilableSource\Expression\ClassDependency;
use webignition\BasilCompilableSourceFactory\ClassDefinitionFactory;
use webignition\BasilCompilableSourceFactory\Exception\UnsupportedStepException;
use webignition\BasilModels\Test\TestInterface;

class Compiler
{
    private ClassDefinitionFactory $classDefinitionFactory;
    private CompiledClassResolver $compiledClassResolver;

    public function __construct(
        ClassDefinitionFactory $classDefinitionFactory,
        CompiledClassResolver $compiledClassResolver
    ) {
        $this->classDefinitionFactory = $classDefinitionFactory;
        $this->compiledClassResolver = $compiledClassResolver;
    }

    public static function createCompiler(): self
    {
        return new Compiler(
            ClassDefinitionFactory::createFactory(),
            CompiledClassResolver::createResolver(
                ExternalVariableIdentifiersFactory::create()
            )
        );
    }

    /**
     * @param TestInterface $test
     * @param string $fullyQualifiedBaseClass
     *
     * @return CompiledTest
     *
     * @throws UnresolvedPlaceholderException
     * @throws UnsupportedStepException
     */
    public function compile(TestInterface $test, string $fullyQualifiedBaseClass): CompiledTest
    {
        $classDefinition = $this->classDefinitionFactory->createClassDefinition($test);
        if ($classDefinition instanceof ClassDefinition) {
            $classDefinition->setBaseClass(new ClassDependency($fullyQualifiedBaseClass));
        }

        $code = $this->compiledClassResolver->resolve($classDefinition->render());

        return new CompiledTest($test, $code, $classDefinition->getName());
    }
}
