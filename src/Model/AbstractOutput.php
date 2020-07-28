<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

abstract class AbstractOutput implements OutputInterface
{
    private Configuration $configuration;
    private int $code;

    public function __construct(Configuration $configuration, int $code)
    {
        $this->configuration = $configuration;
        $this->code = $code;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getData(): array
    {
        return [
            'config' => $this->configuration->getData(),
        ];
    }
}
