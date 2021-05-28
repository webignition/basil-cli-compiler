<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\BasilCliCompiler\Model\CompiledTest;
use webignition\BasilCompilableSourceFactory\ClassDefinitionFactory;
use webignition\BasilCompilableSourceFactory\Exception\UnsupportedStepException;
use webignition\BasilModels\Test\TestInterface;
use webignition\Stubble\UnresolvedVariableException;
use webignition\Stubble\VariableResolver;

class Compiler
{
    public function __construct(
        private ClassDefinitionFactory $classDefinitionFactory,
        private CompiledClassResolver $compiledClassResolver,
        private VariableResolver $variableResolver
    ) {
    }

    public static function createCompiler(): self
    {
        return new Compiler(
            ClassDefinitionFactory::createFactory(),
            CompiledClassResolver::createResolver(
                ExternalVariableIdentifiersFactory::create()
            ),
            new VariableResolver()
        );
    }

    /**
     * @throws UnresolvedVariableException
     * @throws UnsupportedStepException
     */
    public function compile(TestInterface $test, string $fullyQualifiedBaseClass): CompiledTest
    {
        $classDefinition = $this->classDefinitionFactory->createClassDefinition($test, $fullyQualifiedBaseClass);
        $resolvedClassDefinition = $this->variableResolver->resolveAndIgnoreUnresolvedVariables($classDefinition);

        $code = $this->compiledClassResolver->resolve($resolvedClassDefinition);

        return new CompiledTest(
            $code,
            $classDefinition->getSignature()->getName()
        );
    }
}
