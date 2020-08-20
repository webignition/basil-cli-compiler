<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use Symfony\Component\Console\Output\OutputInterface as ConsoleOutputInterface;
use Symfony\Component\Yaml\Yaml;
use webignition\BasilCompilerModels\ErrorOutputInterface;
use webignition\BasilCompilerModels\OutputInterface;

class OutputRenderer
{
    private const YAML_DUMP_INLINE_DEPTH = 4;

    private ConsoleOutputInterface $consoleOutput;

    public function __construct(ConsoleOutputInterface $consoleOutput)
    {
        $this->consoleOutput = $consoleOutput;
    }

    public function render(OutputInterface $commandOutput): int
    {
        $this->consoleOutput->writeln(Yaml::dump(
            $commandOutput->getData(),
            self::YAML_DUMP_INLINE_DEPTH
        ));

        return $commandOutput instanceof ErrorOutputInterface ? $commandOutput->getCode() : 0;
    }
}
