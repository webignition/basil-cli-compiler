<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

interface OutputInterface
{
    public function getConfiguration(): Configuration;
    public function getCode(): int;

    /**
     * @return array<mixed>
     */
    public function getData(): array;
}
