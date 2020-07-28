<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

interface OutputInterface
{
    public function getConfiguration(): Configuration;

    /**
     * @return array<mixed>
     */
    public function getData(): array;
}
