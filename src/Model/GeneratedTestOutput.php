<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

use webignition\BasilModels\Test\Configuration as TestConfiguration;
use webignition\BasilModels\Test\ConfigurationInterface;

class GeneratedTestOutput
{
    private string $source;
    private string $target;
    private ConfigurationInterface $configuration;

    public function __construct(ConfigurationInterface $configuration, string $source, string $target)
    {
        $this->configuration = $configuration;
        $this->source = $source;
        $this->target = $target;
    }

    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @return array<string, string|array<string, string>>
     */
    public function getData(): array
    {
        return [
            'configuration' => [
                'browser' => $this->configuration->getBrowser(),
                'url' => $this->configuration->getUrl(),
            ],
            'source' => $this->source,
            'target' => $this->target,
        ];
    }

    /**
     * @param array<string, string> $data
     *
     * @return GeneratedTestOutput
     */
    public static function fromArray(array $data): GeneratedTestOutput
    {
        return new GeneratedTestOutput(
            new TestConfiguration(
                $data['configuration']['browser'],
                $data['configuration']['url']
            ),
            $data['source'],
            $data['target']
        );
    }
}
