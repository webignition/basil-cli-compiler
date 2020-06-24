<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use webignition\BasilCliCompiler\Command\GenerateCommand;
use webignition\BasilCompiler\Compiler;
use webignition\BasilLoader\SourceLoader;

class CommandFactory
{
    private string $projectRootPath;

    public function __construct(string $projectRootPath)
    {
        $this->projectRootPath = $projectRootPath;
    }

    public static function createFactory(): self
    {
        return new CommandFactory(
            (new ProjectRootPathProvider())->get()
        );
    }

    public function createGenerateCommand(): GenerateCommand
    {
        $externalVariableIdentifiers = ExternalVariableIdentifiersFactory::create();
        $configurationValidator = new ConfigurationValidator();

        return new GenerateCommand(
            SourceLoader::createLoader(),
            new TestWriter(
                Compiler::create($externalVariableIdentifiers),
                new PhpFileCreator(),
            ),
            $this->projectRootPath,
            new ConfigurationFactory($this->projectRootPath),
            $configurationValidator,
            new ErrorOutputFactory($configurationValidator, new ValidatorInvalidResultSerializer()),
        );
    }
}
