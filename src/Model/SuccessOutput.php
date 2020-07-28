<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

class SuccessOutput extends AbstractOutput
{
    public const CODE = 0;

    /**
     * @var GeneratedTestOutput[]
     */
    private array $output;

    /**
     * @param Configuration $configuration
     * @param GeneratedTestOutput[] $output
     */
    public function __construct(Configuration $configuration, array $output)
    {
        parent::__construct($configuration, self::CODE);

        $this->output = $output;
    }

    /**
     * @return GeneratedTestOutput[]
     */
    public function getOutput(): array
    {
        return $this->output;
    }

    /**
     * @return string[]
     */
    public function getTestPaths(): array
    {
        $targetDirectory = $this->getConfiguration()->getTarget();

        $testPaths = [];

        foreach ($this->getOutput() as $generatedTestOutput) {
            $testPaths[] = $targetDirectory . '/' . $generatedTestOutput->getTarget();
        }

        return $testPaths;
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        $generatedTestData = [];
        foreach ($this->output as $generatedTestOutput) {
            $generatedTestData[] = $generatedTestOutput->getData();
        }

        $serializedData = parent::getData();
        $serializedData['output'] = $generatedTestData;

        return $serializedData;
    }

    /**
     * @param array<mixed> $data
     *
     * @return SuccessOutput
     */
    public static function fromArray(array $data): SuccessOutput
    {
        $configData = $data['config'] ?? [];
        $outputData = $data['output'] ?? [];

        $output = [];

        foreach ($outputData as $generatedTestOutput) {
            $output[] = GeneratedTestOutput::fromArray($generatedTestOutput);
        }

        return new SuccessOutput(
            Configuration::fromArray($configData),
            $output
        );
    }
}
