<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Tests\Services\ProjectRootPathProvider;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\ErrorOutput;

trait InvalidTestDataProviderTrait
{
    public function invalidTestDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        $testPath = $root . '/tests/Fixtures/basil/InvalidTest/invalid-configuration.yml';
        $testAbsolutePath = '' . $testPath;

        $testSuitePath = $root . '/tests/Fixtures/basil/InvalidTestSuite/imports-invalid-test.yml';
        $testSuiteAbsolutePath = '' . $testSuitePath;

        return [
            'test has invalid configuration' => [
                'input' => [
                    '--source' => $testPath,
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_INVALID_TEST,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $testAbsolutePath,
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Invalid test at path "' .
                    $testAbsolutePath .
                    '": test-configuration-invalid',
                    ErrorOutput::CODE_LOADER_INVALID_TEST,
                    [
                        'test_path' => $testAbsolutePath,
                        'validation_result' => [
                            'type' => 'test',
                            'reason' => 'test-configuration-invalid',
                            'previous' => [
                                'type' => 'test-configuration',
                                'reason' => 'test-configuration-browser-empty',
                            ],
                        ],
                    ]
                ),
            ],
            'test suite imports test with invalid configuration' => [
                'input' => [
                    '--source' => $testSuitePath,
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_INVALID_TEST,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $testSuiteAbsolutePath,
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Invalid test at path "' .
                    $testAbsolutePath .
                    '": test-configuration-invalid',
                    ErrorOutput::CODE_LOADER_INVALID_TEST,
                    [
                        'test_path' => $testAbsolutePath,
                        'validation_result' => [
                            'type' => 'test',
                            'reason' => 'test-configuration-invalid',
                            'previous' => [
                                'type' => 'test-configuration',
                                'reason' => 'test-configuration-browser-empty',
                            ],
                        ],
                    ]
                ),
            ],
        ];
    }
}
