<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\ErrorOutput;
use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;

trait ParseExceptionDataProviderTrait
{
    public function parseExceptionDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        return [
            'test declares step, step contains unparseable action' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTest/unparseable-action.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/unparseable-action.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unparseable test',
                    ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                    [
                        'type' => 'test',
                        'test_path' => $root . '/tests/Fixtures/basil/InvalidTest/unparseable-action.yml',
                        'step_name' => 'contains unparseable action',
                        'statement_type' => 'action',
                        'statement' => 'click invalid-identifier',
                        'reason' => 'invalid-identifier',

                    ]
                ),
            ],
            'test declares step, step contains unparseable assertion' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTest/unparseable-assertion.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/unparseable-assertion.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unparseable test',
                    ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                    [
                        'type' => 'test',
                        'test_path' => $root . '/tests/Fixtures/basil/InvalidTest/unparseable-assertion.yml',
                        'step_name' => 'contains unparseable assertion',
                        'statement_type' => 'assertion',
                        'statement' => '$page.url is',
                        'reason' => 'empty-value',

                    ]
                ),
            ],
            'test imports step, step contains unparseable action' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTest/import-unparseable-action.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/import-unparseable-action.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unparseable step',
                    ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                    [
                        'type' => 'step',
                        'test_path' => $root . '/tests/Fixtures/basil/InvalidTest/import-unparseable-action.yml',
                        'step_path' => $root . '/tests/Fixtures/basil/Step/unparseable-action.yml',
                        'statement_type' => 'action',
                        'statement' => 'click invalid-identifier',
                        'reason' => 'invalid-identifier',

                    ]
                ),
            ],
            'test imports step, step contains unparseable assertion' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTest/import-unparseable-assertion.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/import-unparseable-assertion.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unparseable step',
                    ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                    [
                        'type' => 'step',
                        'test_path' => $root . '/tests/Fixtures/basil/InvalidTest/import-unparseable-assertion.yml',
                        'step_path' => $root . '/tests/Fixtures/basil/Step/unparseable-assertion.yml',
                        'statement_type' => 'assertion',
                        'statement' => '$page.url is',
                        'reason' => 'empty-value',

                    ]
                ),
            ],
            'test suite imports test which declares step, step contains unparseable action' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTestSuite/imports-test-declaring-unparseable-action.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTestSuite/imports-test-declaring-unparseable-action.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unparseable test',
                    ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                    [
                        'type' => 'test',
                        'test_path' => $root . '/tests/Fixtures/basil/InvalidTest/unparseable-action.yml',
                        'step_name' => 'contains unparseable action',
                        'statement_type' => 'action',
                        'statement' => 'click invalid-identifier',
                        'reason' => 'invalid-identifier',

                    ]
                ),
            ],
            'test suite imports test which imports step, step contains unparseable action' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTestSuite/imports-test-importing-unparseable-action.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTestSuite/imports-test-importing-unparseable-action.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unparseable step',
                    ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                    [
                        'type' => 'step',
                        'test_path' => $root . '/tests/Fixtures/basil/InvalidTest/import-unparseable-action.yml',
                        'step_path' => $root . '/tests/Fixtures/basil/Step/unparseable-action.yml',
                        'statement_type' => 'action',
                        'statement' => 'click invalid-identifier',
                        'reason' => 'invalid-identifier',

                    ]
                ),
            ],
            'test declares step, step contains non-array actions data' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTest/non-array-actions-data.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/non-array-actions-data.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unparseable test',
                    ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                    [
                        'type' => 'test',
                        'test_path' => $root . '/tests/Fixtures/basil/InvalidTest/non-array-actions-data.yml',
                        'step_name' => 'non-array actions data',
                        'reason' => 'invalid-actions-data',

                    ]
                ),
            ],
            'test declares step, step contains non-array assertions data' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTest/non-array-assertions-data.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/non-array-assertions-data.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unparseable test',
                    ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                    [
                        'type' => 'test',
                        'test_path' => $root . '/tests/Fixtures/basil/InvalidTest/non-array-assertions-data.yml',
                        'step_name' => 'non-array assertions data',
                        'reason' => 'invalid-assertions-data',

                    ]
                ),
            ],
            'test imports step, step contains non-array actions data' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTest/import-non-array-actions-data.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/import-non-array-actions-data.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unparseable step',
                    ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                    [
                        'type' => 'step',
                        'test_path' => $root . '/tests/Fixtures/basil/InvalidTest/import-non-array-actions-data.yml',
                        'step_path' => $root . '/tests/Fixtures/basil/Step/non-array-actions-data.yml',
                        'reason' => 'invalid-actions-data',
                    ]
                ),
            ],
            'test imports step, step contains non-array assertions data' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTest/import-non-array-assertions-data.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/import-non-array-assertions-data.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unparseable step',
                    ErrorOutput::CODE_LOADER_UNPARSEABLE_DATA,
                    [
                        'type' => 'step',
                        'test_path' => $root . '/tests/Fixtures/basil/InvalidTest/import-non-array-assertions-data.yml',
                        'step_path' => $root . '/tests/Fixtures/basil/Step/non-array-assertions-data.yml',
                        'reason' => 'invalid-assertions-data',
                    ]
                ),
            ],
        ];
    }
}