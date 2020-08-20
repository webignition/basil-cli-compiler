<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use Symfony\Component\Console\Output\OutputInterface;
use webignition\BasilCliCompiler\Command\GenerateCommand;
use webignition\BasilLoader\SourceLoader;

class CommandFactory
{
    public static function createGenerateCommand(OutputInterface $commandOutput): GenerateCommand
    {
        return new GenerateCommand(
            SourceLoader::createLoader(),
            Compiler::createCompiler(),
            TestWriter::createWriter(),
            new ErrorOutputFactory(new ValidatorInvalidResultSerializer()),
            new OutputRenderer($commandOutput)
        );
    }
}
