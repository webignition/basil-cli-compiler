<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use webignition\BasilCliCompiler\Services\VariablePlaceholderResolver;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;

class VariablePlaceholderResolverTest extends \PHPUnit\Framework\TestCase
{
    private VariablePlaceholderResolver $variablePlaceholderResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->variablePlaceholderResolver = new VariablePlaceholderResolver();
    }

    /**
     * @dataProvider resolveDataProvider
     */
    public function testResolve(ResolvableInterface $resolvable, string $expectedResolvedTemplate): void
    {
        $resolvedContent = $this->variablePlaceholderResolver->resolve($resolvable);

        $this->assertSame($expectedResolvedTemplate, $resolvedContent);
    }

    /**
     * @return array[]
     */
    public function resolveDataProvider(): array
    {
        return [
            'contains parent > child descendant identifier' => [
                'resolvable' => new Resolvable('method(\'$"{{ $".parent" }} .child"\')', []),
                'expectedResolvedTemplate' => 'method(\'$"{{ $".parent" }} .child"\')',
            ],
            'contains grandparent > parent > child descendant identifier' => [
                'resolvable' => new Resolvable('method(\'$"{{ $"{{ $".grandparent" }} .parent" }} .child"\')', []),
                'expectedResolvedTemplate' => 'method(\'$"{{ $"{{ $".grandparent" }} .parent" }} .child"\')',
            ],
        ];
    }
}
