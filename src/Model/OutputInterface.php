<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

use JsonSerializable;

interface OutputInterface extends JsonSerializable
{
    public function getConfiguration(): Configuration;
    public function getCode(): int;

    /**
     * @return array<mixed>
     */
    public function getData(): array;
}
