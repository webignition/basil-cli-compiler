<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\BasilCliCompiler\Exception\UnresolvedPlaceholderException;
use webignition\BasilCliCompiler\Model\ExternalVariableIdentifiers;

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
     * @throws UnresolvedPlaceholderException
     */
    public function resolve(string $compiledClass): string
    {
        $compiledClassLines = explode("\n", $compiledClass);

        $resolvedLines = [];

        foreach ($compiledClassLines as $line) {
            $resolvedLines[] = $this->variablePlaceholderResolver->resolve(
                $line,
                $this->externalVariableIdentifiers->get()
            );
        }

        return implode("\n", $resolvedLines);
    }
}
