<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\BasilCliCompiler\Model\ExternalVariableIdentifiers;
use webignition\Stubble\Resolvable;
use webignition\Stubble\UnresolvedVariableException;

class CompiledClassResolver
{
    private ExternalVariableIdentifiers $externalVariableIdentifiers;
    private VariablePlaceholderResolver $variablePlaceholderResolver;

    public function __construct(
        ExternalVariableIdentifiers $externalVariableIdentifiers,
        VariablePlaceholderResolver $variablePlaceholderResolver
    ) {
        $this->externalVariableIdentifiers = $externalVariableIdentifiers;
        $this->variablePlaceholderResolver = $variablePlaceholderResolver;
    }

    public static function createResolver(ExternalVariableIdentifiers $externalVariableIdentifiers): self
    {
        return new CompiledClassResolver(
            $externalVariableIdentifiers,
            new VariablePlaceholderResolver()
        );
    }

    /**
     * @param string $compiledClass
     *
     * @return string
     *
     * @throws UnresolvedVariableException
     */
    public function resolve(string $compiledClass): string
    {
        $compiledClassLines = explode("\n", $compiledClass);

        $resolvedLines = [];

        foreach ($compiledClassLines as $line) {
            $resolvedLines[] = $this->variablePlaceholderResolver->resolve(
                new Resolvable(
                    $line,
                    $this->externalVariableIdentifiers->get()
                )
            );
        }

        return implode("\n", $resolvedLines);
    }
}
