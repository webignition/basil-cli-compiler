<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\ErrorOutput;
use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;

trait CircularStepImportDataProviderTrait
{
    public function circularStepImportDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        return [
            'test imports step which imports self' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTest/invalid.import-circular-reference-self.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_CIRCULAR_STEP_IMPORT,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/invalid.import-circular-reference-self.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Circular step import "circular_reference_self"',
                    ErrorOutput::CODE_LOADER_CIRCULAR_STEP_IMPORT,
                    [
                        'import_name' => 'circular_reference_self',
                    ]
                ),
            ],
            'test imports step which step imports self' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTest/invalid.import-circular-reference-indirect.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_CIRCULAR_STEP_IMPORT,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/invalid.import-circular-reference-indirect.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Circular step import "circular_reference_self"',
                    ErrorOutput::CODE_LOADER_CIRCULAR_STEP_IMPORT,
                    [
                        'import_name' => 'circular_reference_self',
                    ]
                ),
            ],
        ];
    }
}