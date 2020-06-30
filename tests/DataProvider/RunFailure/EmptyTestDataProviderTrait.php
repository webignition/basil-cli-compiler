<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\ErrorOutput;
use webignition\BasilCliCompiler\Tests\Services\ProjectRootPathProvider;

trait EmptyTestDataProviderTrait
{
    public function emptyTestDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        $emptyTestPath = $root . '/tests/Fixtures/basil/InvalidTest/empty.yml';
        $emptyTestAbsolutePath = '' . $emptyTestPath;

        return [
            'test file is empty' => [
                'input' => [
                    '--source' => $emptyTestPath,
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_EMPTY_TEST,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $emptyTestAbsolutePath,
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Empty test at path "' . $emptyTestAbsolutePath . '"',
                    ErrorOutput::CODE_LOADER_EMPTY_TEST,
                    [
                        'path' => $emptyTestAbsolutePath,
                    ]
                ),
            ],
        ];
    }
}
