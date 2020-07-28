<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

class SuiteManifest extends AbstractOutput
{
    /**
     * @var TestManifest[]
     */
    private array $testManifests;

    /**
     * @param Configuration $configuration
     * @param TestManifest[] $output
     */
    public function __construct(Configuration $configuration, array $output)
    {
        parent::__construct($configuration);

        $this->testManifests = $output;
    }

    /**
     * @return TestManifest[]
     */
    public function getTestManifests(): array
    {
        return $this->testManifests;
    }

    /**
     * @return string[]
     */
    public function getTestPaths(): array
    {
        $targetDirectory = $this->getConfiguration()->getTarget();

        $testPaths = [];

        foreach ($this->getTestManifests() as $testManifest) {
            $testPaths[] = $targetDirectory . '/' . $testManifest->getTarget();
        }

        return $testPaths;
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        $manifestDataCollection = [];
        foreach ($this->testManifests as $testManifest) {
            $manifestDataCollection[] = $testManifest->getData();
        }

        $serializedData = parent::getData();
        $serializedData['output'] = $manifestDataCollection;

        return $serializedData;
    }

    /**
     * @param array<mixed> $data
     *
     * @return SuiteManifest
     */
    public static function fromArray(array $data): SuiteManifest
    {
        $configData = $data['config'] ?? [];
        $manifestDataCollection = $data['output'] ?? [];

        $output = [];

        foreach ($manifestDataCollection as $manifestData) {
            $output[] = TestManifest::fromArray($manifestData);
        }

        return new SuiteManifest(
            Configuration::fromArray($configData),
            $output
        );
    }
}
