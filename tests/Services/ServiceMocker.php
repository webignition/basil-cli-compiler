<?php

namespace webignition\BasilCliCompiler\Tests\Services;

use webignition\BasilCliCompiler\Services\Compiler;
use webignition\BasilCompilableSourceFactory\ClassDefinitionFactory;
use webignition\BasilCompilableSourceFactory\ClassNameFactory;
use webignition\ObjectReflector\ObjectReflector;

class ServiceMocker
{
    /**
     * @param string[] $classNames
     */
    public function mockClassNameFactoryOnCompiler(Compiler $compiler, array $classNames): Compiler
    {
        $classDefinitionFactory = ObjectReflector::getProperty($compiler, 'classDefinitionFactory');

        $classNameFactory = \Mockery::mock(ClassNameFactory::class);
        $classNameFactory
            ->shouldReceive('create')
            ->andReturnValues($classNames)
        ;

        ObjectReflector::setProperty(
            $classDefinitionFactory,
            ClassDefinitionFactory::class,
            'classNameFactory',
            $classNameFactory
        );

        ObjectReflector::setProperty(
            $compiler,
            Compiler::class,
            'classDefinitionFactory',
            $classDefinitionFactory
        );

        return $compiler;
    }
}
