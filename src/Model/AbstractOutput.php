<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

abstract class AbstractOutput implements OutputInterface
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function getData(): array
    {
        return [
            'config' => $this->configuration->getData(),
        ];
    }
}
