<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

class Configuration
{
    private string $source;
    private string $target;
    private string $baseClass;

    public function __construct(string $source, string $target, string $baseClass)
    {
        $this->source = $source;
        $this->target = $target;
        $this->baseClass = $baseClass;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getBaseClass(): string
    {
        return $this->baseClass;
    }

    /**
     * @return array<string, string>
     */
    public function getData(): array
    {
        return [
            'source' => $this->source,
            'target' => $this->target,
            'base-class' => $this->baseClass,
        ];
    }

    /**
     * @param array<mixed> $data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new Configuration(
            $data['source'] ?? '',
            $data['target'] ?? '',
            $data['base-class'] ?? ''
        );
    }
}
