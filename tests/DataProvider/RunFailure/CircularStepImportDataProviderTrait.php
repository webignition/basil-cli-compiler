<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Services\ErrorOutputFactory;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\ErrorOutput;

trait CircularStepImportDataProviderTrait
{
    public function circularStepImportDataProvider(): array
    {
        $root = getcwd();

        return [
            'test imports step which imports self' => [
                'input' => [
                    '--source' =>
                        $root . '/tests/Fixtures/basil/InvalidTest/invalid.import-circular-reference-self.yml',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => ErrorOutputFactory::CODE_LOADER_CIRCULAR_STEP_IMPORT,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/invalid.import-circular-reference-self.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Circular step import "circular_reference_self"',
                    ErrorOutputFactory::CODE_LOADER_CIRCULAR_STEP_IMPORT,
                    [
                        'import_name' => 'circular_reference_self',
                    ]
                ),
            ],
            'test imports step which step imports self' => [
                'input' => [
                    '--source' =>
                        $root . '/tests/Fixtures/basil/InvalidTest/invalid.import-circular-reference-indirect.yml',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => ErrorOutputFactory::CODE_LOADER_CIRCULAR_STEP_IMPORT,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/invalid.import-circular-reference-indirect.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Circular step import "circular_reference_self"',
                    ErrorOutputFactory::CODE_LOADER_CIRCULAR_STEP_IMPORT,
                    [
                        'import_name' => 'circular_reference_self',
                    ]
                ),
            ],
        ];
    }
}
