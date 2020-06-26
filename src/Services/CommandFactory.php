<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\BasilCliCompiler\Command\GenerateCommand;
use webignition\BasilCompilableSourceFactory\ClassDefinitionFactory;
use webignition\BasilCompiler\Compiler;
use webignition\BasilLoader\SourceLoader;

class CommandFactory
{
    public static function createGenerateCommand(): GenerateCommand
    {
        $projectRootPath = (new ProjectRootPathProvider())->get();
        $externalVariableIdentifiers = ExternalVariableIdentifiersFactory::create();
        $configurationValidator = new ConfigurationValidator();

        return new GenerateCommand(
            SourceLoader::createLoader(),
            new TestWriter(
                Compiler::create($externalVariableIdentifiers),
                new PhpFileCreator(),
                ClassDefinitionFactory::createFactory()
            ),
            new ConfigurationFactory($projectRootPath),
            $configurationValidator,
            new ErrorOutputFactory($configurationValidator, new ValidatorInvalidResultSerializer()),
            $projectRootPath
        );
    }
}
