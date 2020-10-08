<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\Stubble\VariableResolver;

class VariablePlaceholderResolver extends VariableResolver
{
    public function __construct()
    {
        parent::__construct();

        $this->addUnresolvedVariableDecider(function (string $variable) {
            $prefix = '$"';
            $prefixLength = strlen($prefix);
            $variableLength = strlen($variable);

            if ($variableLength < $prefixLength) {
                return false;
            }

            $variableStart = substr($variable, 0, $prefixLength);

            return $variableStart === $prefix;
        });
    }
}
