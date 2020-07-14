<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

abstract class AbstractOutput implements OutputInterface
{
    protected const STATUS_SUCCESS = 'success';
    protected const STATUS_FAILURE = 'failure';

    private Configuration $configuration;
    private string $status;
    private int $code;

    public function __construct(Configuration $configuration, string $status, int $code)
    {
        $this->configuration = $configuration;
        $this->status = $status;
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
            'status' => $this->status,
        ];
    }
}
