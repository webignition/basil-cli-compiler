<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use Symfony\Component\Console\Output\OutputInterface;
use webignition\BasilCliCompiler\Command\GenerateCommand;
use webignition\BasilLoader\TestLoader;

class CommandFactory
{
    public static function createGenerateCommand(OutputInterface $stdout, OutputInterface $stderr): GenerateCommand
    {
        return new GenerateCommand(
            TestLoader::createLoader(),
            Compiler::createCompiler(),
            TestWriter::createWriter(),
            new ErrorOutputFactory(new ValidatorInvalidResultSerializer()),
            new OutputRenderer($stdout, $stderr)
        );
    }
}
