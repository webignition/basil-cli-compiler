<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Services\ErrorOutputFactory;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\ErrorOutput;

trait UnknownTestDataProviderTrait
{
    public function unknownTestDataProvider(): array
    {
        $root = getcwd();

        return [
            'test suite imports test that does not exist' => [
                'input' => [
                    '--source' => $root . '/tests/Fixtures/basil/InvalidTestSuite/imports-non-existent-test.yml',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => ErrorOutputFactory::CODE_LOADER_UNKNOWN_TEST,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTestSuite/imports-non-existent-test.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unknown test "' . $root . '/tests/Fixtures/basil/Test/non-existent.yml"',
                    ErrorOutputFactory::CODE_LOADER_UNKNOWN_TEST,
                    [
                        'import_name' => $root . '/tests/Fixtures/basil/Test/non-existent.yml',
                    ]
                ),
            ],
        ];
    }
}
