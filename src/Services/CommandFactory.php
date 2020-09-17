<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use Symfony\Component\Console\Output\OutputInterface;
use webignition\BasilCliCompiler\Command\GenerateCommand;
use webignition\BasilLoader\TestLoader;

class CommandFactory
{
    private const TARGET_ARG_START_PATTERN = '/^--' . GenerateCommand::OPTION_TARGET . '=/';

    /**
     * @param OutputInterface $stdout
     * @param OutputInterface $stderr
     * @param array<int, string> $cliArguments
     *
     * @return GenerateCommand
     */
    public static function createGenerateCommand(
        OutputInterface $stdout,
        OutputInterface $stderr,
        array $cliArguments
    ): GenerateCommand {
        return new GenerateCommand(
            TestLoader::createLoader(),
            Compiler::createCompiler(),
            TestWriter::createWriter(self::getOutputDirectory($cliArguments)),
            new ErrorOutputFactory(new ValidatorInvalidResultSerializer()),
            new OutputRenderer($stdout, $stderr)
        );
    }

    /**
     * @param array<int, string> $cliArguments
     *
     * @return string
     */
    private static function getOutputDirectory(array $cliArguments): string
    {
        foreach ($cliArguments as $cliArgument) {
            if (preg_match(self::TARGET_ARG_START_PATTERN, $cliArgument)) {
                return (string) preg_replace(self::TARGET_ARG_START_PATTERN, '', $cliArgument);
            }
        }

        return '';
    }
}
