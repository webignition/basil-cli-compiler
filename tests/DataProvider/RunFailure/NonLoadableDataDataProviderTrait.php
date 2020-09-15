<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Services\ErrorOutputFactory;
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
                'expectedExitCode' => ErrorOutputFactory::CODE_LOADER_INVALID_YAML,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/invalid.unparseable.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Malformed inline YAML string: ""chrome" at line 3 (near "- "chrome").',
                    ErrorOutputFactory::CODE_LOADER_INVALID_YAML,
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
                'expectedExitCode' => ErrorOutputFactory::CODE_LOADER_INVALID_YAML,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/invalid.not-an-array.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Data is not an array',
                    ErrorOutputFactory::CODE_LOADER_INVALID_YAML,
                    [
                        'path' => $root . '/tests/Fixtures/basil/InvalidTest/invalid.not-an-array.yml',
                    ]
                ),
            ],
        ];
    }
}
