<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Model;

use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\GeneratedTestOutput;
use webignition\BasilCliCompiler\Model\SuccessOutput;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;

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
            new GeneratedTestOutput('test1.yml', 'GeneratedTest1.php'),
            new GeneratedTestOutput('test2.yml', 'GeneratedTest2.php'),
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
            new GeneratedTestOutput('test1.yml', 'GeneratedTest1.php'),
            new GeneratedTestOutput('test2.yml', 'GeneratedTest2.php'),
        ];

        return [
            'empty generated test output collection' => [
                'output' => new SuccessOutput($configuration, []),
                'expectedData' => [
                    'config' => $configuration->jsonSerialize(),
                    'status' => 'success',
                    'output' => [],
                ],
            ],
            'populated generated test output collection' => [
                'output' => new SuccessOutput($configuration, $generatedTestOutputCollection),
                'expectedData' => [
                    'config' => $configuration->jsonSerialize(),
                    'status' => 'success',
                    'output' => $generatedTestOutputCollection,
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
        $configuration = new Configuration('test.yml', 'build', AbstractBaseTest::class);

        return [
            'empty generated test output collection' => [
                'successOutput' => new SuccessOutput($configuration, []),
                'expectedTestPaths' => [],
            ],
            'populated generated test output collection' => [
                'successOutput' => new SuccessOutput($configuration, [
                    new GeneratedTestOutput('test1.yml', 'GeneratedTest1.php'),
                    new GeneratedTestOutput('test2.yml', 'GeneratedTest2.php'),
                    new GeneratedTestOutput('test3.yml', 'GeneratedTest3.php'),
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
    public function testJsonSerializeTestFromJson(SuccessOutput $output)
    {
        $data = $output->jsonSerialize();

        self::assertEquals(
            $output,
            SuccessOutput::fromJson((string) json_encode($data))
        );
    }

    public function jsonSerializedFromJsonDataProvider(): array
    {
        $configuration = new Configuration('test.yml', 'build', AbstractBaseTest::class);

        return [
            'empty generated test output collection' => [
                'successOutput' => new SuccessOutput($configuration, []),
            ],
            'populated generated test output collection' => [
                'successOutput' => new SuccessOutput($configuration, [
                    new GeneratedTestOutput('test1.yml', 'GeneratedTest1.php'),
                    new GeneratedTestOutput('test2.yml', 'GeneratedTest2.php'),
                    new GeneratedTestOutput('test3.yml', 'GeneratedTest3.php'),
                ]),
            ],
        ];
    }
}
