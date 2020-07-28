<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Model;

use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\GeneratedTestOutput;
use webignition\BasilCliCompiler\Model\SuccessOutput;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;
use webignition\BasilModels\Test\Configuration as TestModelConfiguration;

class SuccessOutputTest extends AbstractBaseTest
{
    private SuccessOutput $output;
    private Configuration $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new Configuration('test.yml', 'build', AbstractBaseTest::class);
        $this->output = new SuccessOutput($this->configuration, []);
    }

    public function testGetConfiguration()
    {
        self::assertSame($this->configuration, $this->output->getConfiguration());
    }

    public function testGetCode()
    {
        self::assertSame(SuccessOutput::CODE, $this->output->getCode());
    }

    public function testGetOutput()
    {
        $generatedTestOutputCollection = [
            new GeneratedTestOutput(
                new TestModelConfiguration('chrome', 'http://example.com'),
                'test1.yml',
                'GeneratedTest1.php'
            ),
            new GeneratedTestOutput(
                new TestModelConfiguration('firefox', 'http://example.com'),
                'test2.yml',
                'GeneratedTest2.php'
            ),
        ];

        $output = new SuccessOutput($this->configuration, $generatedTestOutputCollection);
        self::assertSame($generatedTestOutputCollection, $output->getOutput());
    }

    /**
     * @dataProvider getDataDataProvider
     *
     * @param SuccessOutput $output
     * @param array<mixed> $expectedData
     */
    public function testGetData(SuccessOutput $output, array $expectedData)
    {
        self::assertSame($expectedData, $output->getData());
    }

    public function getDataDataProvider(): array
    {
        $configuration = new Configuration('test.yml', 'build', AbstractBaseTest::class);
        $generatedTestOutputCollection = [
            new GeneratedTestOutput(
                new TestModelConfiguration('chrome', 'http://example.com'),
                'test1.yml',
                'GeneratedTest1.php'
            ),
            new GeneratedTestOutput(
                new TestModelConfiguration('firefox', 'http://example.com'),
                'test2.yml',
                'GeneratedTest2.php'
            ),
        ];

        return [
            'empty generated test output collection' => [
                'output' => new SuccessOutput($configuration, []),
                'expectedData' => [
                    'config' => $configuration->getData(),
                    'output' => [],
                ],
            ],
            'populated generated test output collection' => [
                'output' => new SuccessOutput($configuration, $generatedTestOutputCollection),
                'expectedData' => [
                    'config' => $configuration->getData(),
                    'output' => [
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
     * @param SuccessOutput $successOutput
     * @param array<string> $expectedTestPaths
     */
    public function testGetTestPaths(SuccessOutput $successOutput, array $expectedTestPaths)
    {
        self::assertSame($expectedTestPaths, $successOutput->getTestPaths());
    }

    public function getTestPathsDataProvider(): array
    {
        $compilerConfiguration = new Configuration('test.yml', 'build', AbstractBaseTest::class);
        $testConfiguration = new TestModelConfiguration('chrome', 'http://example.com');

        return [
            'empty generated test output collection' => [
                'successOutput' => new SuccessOutput($compilerConfiguration, []),
                'expectedTestPaths' => [],
            ],
            'populated generated test output collection' => [
                'successOutput' => new SuccessOutput($compilerConfiguration, [
                    new GeneratedTestOutput($testConfiguration, 'test1.yml', 'GeneratedTest1.php'),
                    new GeneratedTestOutput($testConfiguration, 'test2.yml', 'GeneratedTest2.php'),
                    new GeneratedTestOutput($testConfiguration, 'test3.yml', 'GeneratedTest3.php'),
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
    public function testGetDataFromArray(SuccessOutput $output)
    {
        self::assertEquals(
            $output,
            SuccessOutput::fromArray($output->getData())
        );
    }

    public function jsonSerializedFromJsonDataProvider(): array
    {
        $compilerConfiguration = new Configuration('test.yml', 'build', AbstractBaseTest::class);
        $testConfiguration = new TestModelConfiguration('chrome', 'http://example.com');

        return [
            'empty generated test output collection' => [
                'successOutput' => new SuccessOutput($compilerConfiguration, []),
            ],
            'populated generated test output collection' => [
                'successOutput' => new SuccessOutput($compilerConfiguration, [
                    new GeneratedTestOutput($testConfiguration, 'test1.yml', 'GeneratedTest1.php'),
                    new GeneratedTestOutput($testConfiguration, 'test2.yml', 'GeneratedTest2.php'),
                    new GeneratedTestOutput($testConfiguration, 'test3.yml', 'GeneratedTest3.php'),
                ]),
            ],
        ];
    }
}
