<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use Symfony\Component\Console\Input\InputInterface;
use webignition\BasilCliCompiler\Model\Options;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\ConfigurationInterface;
use webignition\SymfonyConsole\TypedInput\TypedInput;

class ConfigurationFactory
{
    public function create(InputInterface $input): ConfigurationInterface
    {
        $typedInput = new TypedInput($input);

        $rawSource = trim((string) $typedInput->getStringOption(Options::OPTION_SOURCE));
        $rawTarget = trim((string) $typedInput->getStringOption(Options::OPTION_TARGET));
        $baseClass = trim((string) $typedInput->getStringOption(Options::OPTION_BASE_CLASS));

        return new Configuration($rawSource, $rawTarget, $baseClass);
    }
}
