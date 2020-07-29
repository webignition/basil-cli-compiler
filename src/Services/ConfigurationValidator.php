<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\BasilCompilerModels\ConfigurationInterface;
use webignition\BasilCompilerModels\ErrorOutput;

class ConfigurationValidator
{
    public function deriveInvalidConfigurationErrorCode(ConfigurationInterface $configuration): int
    {
        $source = $configuration->getSource();
        if ('' === $source) {
            return ErrorOutput::CODE_COMMAND_CONFIG_SOURCE_INVALID_DOES_NOT_EXIST;
        }

        if (!is_readable($source)) {
            return ErrorOutput::CODE_COMMAND_CONFIG_SOURCE_INVALID_NOT_READABLE;
        }

        $target = $configuration->getTarget();
        if ('' === $target) {
            return ErrorOutput::CODE_COMMAND_CONFIG_TARGET_INVALID_DOES_NOT_EXIST;
        }

        if (!is_dir($target)) {
            return ErrorOutput::CODE_COMMAND_CONFIG_TARGET_INVALID_NOT_A_DIRECTORY;
        }

        if (!is_writable($target)) {
            return ErrorOutput::CODE_COMMAND_CONFIG_TARGET_INVALID_NOT_WRITABLE;
        }

        return 0;
    }
}
