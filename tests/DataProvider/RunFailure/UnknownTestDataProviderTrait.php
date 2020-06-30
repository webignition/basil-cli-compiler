<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\ErrorOutput;
use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;

trait UnknownTestDataProviderTrait
{
    public function unknownTestDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        return [
            'test suite imports test that does not exist' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTestSuite/imports-non-existent-test.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNKNOWN_TEST,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTestSuite/imports-non-existent-test.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unknown test "' . $root . '/tests/Fixtures/basil/Test/non-existent.yml"',
                    ErrorOutput::CODE_LOADER_UNKNOWN_TEST,
                    [
                        'import_name' => $root . '/tests/Fixtures/basil/Test/non-existent.yml',
                    ]
                ),
            ],
        ];
    }
}
