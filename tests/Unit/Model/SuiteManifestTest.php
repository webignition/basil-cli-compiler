<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Model;

use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\SuiteManifest;
use webignition\BasilCliCompiler\Model\TestManifest;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;
use webignition\BasilModels\Test\Configuration as TestModelConfiguration;

class SuiteManifestTest extends AbstractBaseTest
{
    private SuiteManifest $output;
    private Configuration $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new Configuration('test.yml', 'build', AbstractBaseTest::class);
        $this->output = new SuiteManifest($this->configuration, []);
    }

    public function testGetConfiguration()
    {
        self::assertSame($this->configuration, $this->output->getConfiguration());
    }

    public function testGetOutput()
    {
        $testManifests = [
            new TestManifest(
                new TestModelConfiguration('chrome', 'http://example.com'),
                'test1.yml',
                'GeneratedTest1.php'
            ),
            new TestManifest(
                new TestModelConfiguration('firefox', 'http://example.com'),
                'test2.yml',
                'GeneratedTest2.php'
            ),
        ];

        $output = new SuiteManifest($this->configuration, $testManifests);
        self::assertSame($testManifests, $output->getTestManifests());
    }

    /**
     * @dataProvider getDataDataProvider
     *
     * @param SuiteManifest $output
     * @param array<mixed> $expectedData
     */
    public function testGetData(SuiteManifest $output, array $expectedData)
    {
        self::assertSame($expectedData, $output->getData());
    }

    public function getDataDataProvider(): array
    {
        $configuration = new Configuration('test.yml', 'build', AbstractBaseTest::class);
        $testManifests = [
            new TestManifest(
                new TestModelConfiguration('chrome', 'http://example.com'),
                'test1.yml',
                'GeneratedTest1.php'
            ),
            new TestManifest(
                new TestModelConfiguration('firefox', 'http://example.com'),
                'test2.yml',
                'GeneratedTest2.php'
            ),
        ];

        return [
            'empty generated test output collection' => [
                'output' => new SuiteManifest($configuration, []),
                'expectedData' => [
                    'config' => $configuration->getData(),
                    'manifests' => [],
                ],
            ],
            'populated generated test output collection' => [
                'output' => new SuiteManifest($configuration, $testManifests),
                'expectedData' => [
                    'config' => $configuration->getData(),
                    'manifests' => [
                        [
                            'configuration' => [
                                'browser' => 'chrome',
                                'url' => 'http://example.com',
                            ],
                            'source' => 'test1.yml',
                            'target' => 'GeneratedTest1.php',
                        ],
                        [
                            'configuration' => [
                                'browser' => 'firefox',
                                'url' => 'http://example.com',
                            ],
                            'source' => 'test2.yml',
                            'target' => 'GeneratedTest2.php',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getTestPathsDataProvider
     *
     * @param SuiteManifest $suiteManifest
     * @param array<string> $expectedTestPaths
     */
    public function testGetTestPaths(SuiteManifest $suiteManifest, array $expectedTestPaths)
    {
        self::assertSame($expectedTestPaths, $suiteManifest->getTestPaths());
    }

    public function getTestPathsDataProvider(): array
    {
        $compilerConfiguration = new Configuration('test.yml', 'build', AbstractBaseTest::class);
        $testConfiguration = new TestModelConfiguration('chrome', 'http://example.com');

        return [
            'empty generated test output collection' => [
                'suiteManifest' => new SuiteManifest($compilerConfiguration, []),
                'expectedTestPaths' => [],
            ],
            'populated generated test output collection' => [
                'suiteManifest' => new SuiteManifest($compilerConfiguration, [
                    new TestManifest($testConfiguration, 'test1.yml', 'GeneratedTest1.php'),
                    new TestManifest($testConfiguration, 'test2.yml', 'GeneratedTest2.php'),
                    new TestManifest($testConfiguration, 'test3.yml', 'GeneratedTest3.php'),
                ]),
                'expectedTestPaths' => [
                    'build/GeneratedTest1.php',
                    'build/GeneratedTest2.php',
                    'build/GeneratedTest3.php',
                ],
            ],
        ];
    }

    /**
     * @dataProvider jsonSerializedFromJsonDataProvider
     */
    public function testGetDataFromArray(SuiteManifest $output)
    {
        self::assertEquals(
            $output,
            SuiteManifest::fromArray($output->getData())
        );
    }

    public function jsonSerializedFromJsonDataProvider(): array
    {
        $compilerConfiguration = new Configuration('test.yml', 'build', AbstractBaseTest::class);
        $testConfiguration = new TestModelConfiguration('chrome', 'http://example.com');

        return [
            'empty generated test output collection' => [
                'suiteManifest' => new SuiteManifest($compilerConfiguration, []),
            ],
            'populated generated test output collection' => [
                'suiteManifest' => new SuiteManifest($compilerConfiguration, [
                    new TestManifest($testConfiguration, 'test1.yml', 'GeneratedTest1.php'),
                    new TestManifest($testConfiguration, 'test2.yml', 'GeneratedTest2.php'),
                    new TestManifest($testConfiguration, 'test3.yml', 'GeneratedTest3.php'),
                ]),
            ],
        ];
    }
}
