<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

class ApplicationFactory
{
    public static function create(string $projectRootPath): SingleCommandApplication
    {
        $command = CommandFactory::createGenerateCommand($projectRootPath);

        $application = new SingleCommandApplication();
        $application->setName((string) $command->getName());
        $application->setDefinition($command->getDefinition());

        $application
            ->setVersion('0.1-beta')
            ->setCode(function (InputInterface $input, OutputInterface $output) use ($command) {
                return $command->run($input, $output);
            });

        return $application;
    }
}
