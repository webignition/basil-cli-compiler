<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Services\ErrorOutputFactory;
use webignition\BasilCliCompiler\Tests\DataProvider\FixturePaths;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\ErrorOutput;

trait UnknownItemDataProviderTrait
{
    /**
     * @return array[]
     */
    public function unknownItemDataProvider(): array
    {
        return [
            'test declares step, step uses unknown dataset' => [
                'input' => [
                    '--source' => FixturePaths::getInvalidTest() . '/step-uses-unknown-dataset.yml',
                    '--target' => FixturePaths::getTarget(),
                ],
                'expectedExitCode' => ErrorOutputFactory::CODE_LOADER_UNKNOWN_ITEM,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        FixturePaths::getInvalidTest() . '/step-uses-unknown-dataset.yml',
                        FixturePaths::getTarget(),
                        AbstractBaseTest::class
                    ),
                    'Unknown dataset "unknown_data_provider_name"',
                    ErrorOutputFactory::CODE_LOADER_UNKNOWN_ITEM,
                    [
                        'type' => 'dataset',
                        'name' => 'unknown_data_provider_name',
                        'test_path' => FixturePaths::getInvalidTest() . '/step-uses-unknown-dataset.yml',
                        'step_name' => 'step name',
                        'statement' => '',
                    ]
                ),
            ],
            'test declares step, step uses unknown page' => [
                'input' => [
                    '--source' => FixturePaths::getInvalidTest() . '/step-uses-unknown-page.yml',
                    '--target' => FixturePaths::getTarget(),
                ],
                'expectedExitCode' => ErrorOutputFactory::CODE_LOADER_UNKNOWN_ITEM,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        FixturePaths::getInvalidTest() . '/step-uses-unknown-page.yml',
                        FixturePaths::getTarget(),
                        AbstractBaseTest::class
                    ),
                    'Unknown page "unknown_page_import"',
                    ErrorOutputFactory::CODE_LOADER_UNKNOWN_ITEM,
                    [
                        'type' => 'page',
                        'name' => 'unknown_page_import',
                        'test_path' => FixturePaths::getInvalidTest() . '/step-uses-unknown-page.yml',
                        'step_name' => 'step name',
                        'statement' => '',
                    ]
                ),
            ],
            'test declares step, step uses step' => [
                'input' => [
                    '--source' => FixturePaths::getInvalidTest() . '/step-uses-unknown-step.yml',
                    '--target' => FixturePaths::getTarget(),
                ],
                'expectedExitCode' => ErrorOutputFactory::CODE_LOADER_UNKNOWN_ITEM,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        FixturePaths::getInvalidTest() . '/step-uses-unknown-step.yml',
                        FixturePaths::getTarget(),
                        AbstractBaseTest::class
                    ),
                    'Unknown step "unknown_step"',
                    ErrorOutputFactory::CODE_LOADER_UNKNOWN_ITEM,
                    [
                        'type' => 'step',
                        'name' => 'unknown_step',
                        'test_path' => FixturePaths::getInvalidTest() . '/step-uses-unknown-step.yml',
                        'step_name' => 'step name',
                        'statement' => '',
                    ]
                ),
            ],
        ];
    }
}
