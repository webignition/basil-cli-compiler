<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\Stubble\DeciderFactory;
use webignition\Stubble\VariableResolver;

class VariablePlaceholderResolver extends VariableResolver
{
    public function __construct()
    {
        parent::__construct();

        $this->addUnresolvedVariableDecider(DeciderFactory::createAllowByPatternDecider('/^\$".*/'));
    }
}
