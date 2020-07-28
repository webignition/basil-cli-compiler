<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

interface ErrorOutputInterface extends OutputInterface
{
    public function getCode(): int;
}
