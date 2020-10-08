<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use webignition\BasilCliCompiler\Services\VariablePlaceholderResolver;

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
     *
     * @param string $template
     * @param array<string, string> $context
     * @param string $expectedResolvedTemplate
     */
    public function testResolve(string $template, array $context, string $expectedResolvedTemplate)
    {
        $resolvedContent = $this->variablePlaceholderResolver->resolve($template, $context);

        $this->assertSame($expectedResolvedTemplate, $resolvedContent);
    }

    public function resolveDataProvider(): array
    {
        return [
            'contains parent > child descendant identifier' => [
                'template' => 'method(\'$"{{ $".parent" }} .child"\')',
                'context' => [],
                'expectedResolvedTemplate' => 'method(\'$"{{ $".parent" }} .child"\')',
            ],
            'contains grandparent > parent > child descendant identifier' => [
                'template' => 'method(\'$"{{ $"{{ $".grandparent" }} .parent" }} .child"\')',
                'context' => [],
                'expectedResolvedTemplate' => 'method(\'$"{{ $"{{ $".grandparent" }} .parent" }} .child"\')',
            ],
        ];
    }
}
