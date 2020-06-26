<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use webignition\BasilCliCompiler\Exception\UnresolvedPlaceholderException;
use webignition\BasilCliCompiler\Services\CompiledClassResolver;
use webignition\BasilCliCompiler\Services\ExternalVariableIdentifiersFactory;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSourceFactory\VariableNames;

class CompiledClassResolverTest extends \PHPUnit\Framework\TestCase
{
    private CompiledClassResolver $compiledClassResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->compiledClassResolver = CompiledClassResolver::createResolver(
            ExternalVariableIdentifiersFactory::create()
        );
    }

    /**
     * @dataProvider resolveDataProvider
     *
     * @param string $compiledClass
     * @param string $expectedResolvedClass
     *
     * @throws UnresolvedPlaceholderException
     */
    public function testResolve(string $compiledClass, string $expectedResolvedClass)
    {
        $resolvedContent = $this->compiledClassResolver->resolve($compiledClass);

        $this->assertSame($expectedResolvedClass, $resolvedContent);
    }

    public function resolveDataProvider(): array
    {
        return [
            'empty content' => [
                'content' => '',
                'expectedResolvedContent' => '',
            ],
            'non-resolvable content' => [
                'content' => 'non-resolvable content',
                'expectedResolvedContent' => 'non-resolvable content',
            ],
            'resolvable content' => [
                'content' =>
                    (new VariableDependency(VariableNames::ACTION_FACTORY))->render() . "\n" .
                    (new VariableDependency(VariableNames::ASSERTION_FACTORY))->render() . "\n" .
                    (new VariableDependency(VariableNames::DOM_CRAWLER_NAVIGATOR))->render() . "\n" .
                    (new VariableDependency(VariableNames::ENVIRONMENT_VARIABLE_ARRAY))->render() . "\n" .
                    (new VariableDependency(VariableNames::PANTHER_CLIENT))->render() . "\n" .
                    (new VariableDependency(VariableNames::PANTHER_CRAWLER))->render() . "\n" .
                    (new VariableDependency(VariableNames::WEBDRIVER_ELEMENT_INSPECTOR))->render() . "\n" .
                    (new VariableDependency(VariableNames::WEBDRIVER_ELEMENT_MUTATOR))->render(),
                'expectedResolvedContent' =>
                    '$this->actionFactory' . "\n" .
                    '$this->assertionFactory' . "\n" .
                    '$this->navigator' . "\n" .
                    '$_ENV' . "\n" .
                    'self::$client' . "\n" .
                    'self::$crawler' . "\n" .
                    'self::$inspector' . "\n" .
                    'self::$mutator',
            ],
        ];
    }
}
