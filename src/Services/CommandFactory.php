<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\BasilCliCompiler\Command\GenerateCommand;
use webignition\BasilLoader\SourceLoader;

class CommandFactory
{
    public static function createGenerateCommand(string $projectRootPath): GenerateCommand
    {
        $configurationValidator = new ConfigurationValidator();

        return new GenerateCommand(
            SourceLoader::createLoader(),
            Compiler::createCompiler(),
            TestWriter::createWriter(),
            new ConfigurationFactory($projectRootPath),
            $configurationValidator,
            new ErrorOutputFactory($configurationValidator, new ValidatorInvalidResultSerializer()),
            new OutputRenderer(),
            $projectRootPath
        );
    }
}
