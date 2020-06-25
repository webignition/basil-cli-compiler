<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use webignition\BasilCliCompiler\Command\GenerateCommand;

class ApplicationFactory
{
    private GenerateCommand $command;

    public function __construct(CommandFactory $commandFactory)
    {
        $this->command = $commandFactory->createGenerateCommand();
    }

    public static function createFactory(): self
    {
        return new ApplicationFactory(
            CommandFactory::createFactory()
        );
    }

    public function create(): SingleCommandApplication
    {
        $application = new SingleCommandApplication();
        $application->setName((string) $this->command->getName());
        $application->setDefinition($this->command->getDefinition());

        $application
            ->setVersion('0.1-beta')
            ->setCode(function (InputInterface $input, OutputInterface $output) {
                return $this->command->run($input, $output);
            });

        return $application;
    }
}
