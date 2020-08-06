<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\ErrorOutput;

trait NonLoadableDataDataProviderTrait
{
    public function nonLoadableDataDataProvider(): array
    {
        $root = getcwd();

        return [
            'test contains invalid yaml' => [
                'input' => [
                    '--source' => $root . '/tests/Fixtures/basil/InvalidTest/invalid.unparseable.yml',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_INVALID_YAML,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/invalid.unparseable.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unexpected characters near "https://example.com"" at line 3 (near "url: "https://example.com"").',
                    ErrorOutput::CODE_LOADER_INVALID_YAML,
                    [
                        'path' => $root . '/tests/Fixtures/basil/InvalidTest/invalid.unparseable.yml',
                    ]
                ),
            ],
            'test suite imports test containing invalid yaml' => [
                'input' => [
                    '--source' => $root . '/tests/Fixtures/basil/InvalidTestSuite/imports-unparseable.yml',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_INVALID_YAML,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTestSuite/imports-unparseable.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unexpected characters near "https://example.com"" at line 3 (near "url: "https://example.com"").',
                    ErrorOutput::CODE_LOADER_INVALID_YAML,
                    [
                        'path' => $root . '/tests/Fixtures/basil/InvalidTest/invalid.unparseable.yml',
                    ]
                ),
            ],
            'test file contains non-array data' => [
                'input' => [
                    '--source' => $root . '/tests/Fixtures/basil/InvalidTest/invalid.not-an-array.yml',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_INVALID_YAML,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/invalid.not-an-array.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Data is not an array',
                    ErrorOutput::CODE_LOADER_INVALID_YAML,
                    [
                        'path' => $root . '/tests/Fixtures/basil/InvalidTest/invalid.not-an-array.yml',
                    ]
                ),
            ],
            'test suite imports test containing non-array data' => [
                'input' => [
                    '--source' => $root . '/tests/Fixtures/basil/InvalidTestSuite/imports-not-an-array.yml',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_INVALID_YAML,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTestSuite/imports-not-an-array.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Data is not an array',
                    ErrorOutput::CODE_LOADER_INVALID_YAML,
                    [
                        'path' => $root . '/tests/Fixtures/basil/InvalidTest/invalid.not-an-array.yml',
                    ]
                ),
            ],
            'test suite contains unparseable yaml' => [
                'input' => [
                    '--source' => $root . '/tests/Fixtures/basil/InvalidTestSuite/unparseable-yaml.yml',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_INVALID_YAML,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTestSuite/unparseable-yaml.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Malformed inline YAML string: ""../Test/lacking-closing-quote.yml" at line 2.',
                    ErrorOutput::CODE_LOADER_INVALID_YAML,
                    [
                        'path' => $root . '/tests/Fixtures/basil/InvalidTestSuite/unparseable-yaml.yml',
                    ]
                ),
            ],
        ];
    }
}
