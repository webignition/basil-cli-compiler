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
     * @param TestManifest[] $manifests
     */
    public function __construct(Configuration $configuration, array $manifests)
    {
        parent::__construct($configuration);

        $this->testManifests = $manifests;
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
        $manifests = [];
        foreach ($this->testManifests as $testManifest) {
            $manifests[] = $testManifest->getData();
        }

        $serializedData = parent::getData();
        $serializedData['manifests'] = $manifests;

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
        $manifestsData = $data['manifests'] ?? [];

        $manifests = [];

        foreach ($manifestsData as $manifestData) {
            $manifests[] = TestManifest::fromArray($manifestData);
        }

        return new SuiteManifest(Configuration::fromArray($configData), $manifests);
    }
}
