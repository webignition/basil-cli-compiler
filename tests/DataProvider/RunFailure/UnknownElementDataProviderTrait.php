<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Services\ErrorOutputFactory;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\ErrorOutput;

trait UnknownElementDataProviderTrait
{
    public function unknownElementDataProvider(): array
    {
        $root = getcwd();

        return [
            'test declares step, step contains action with unknown element' => [
                'input' => [
                    '--source' => $root . '/tests/Fixtures/basil/InvalidTest/action-contains-unknown-element.yml',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => ErrorOutputFactory::CODE_LOADER_UNKNOWN_ELEMENT,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/action-contains-unknown-element.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unknown element "unknown_element_name"',
                    ErrorOutputFactory::CODE_LOADER_UNKNOWN_ELEMENT,
                    [
                        'element_name' => 'unknown_element_name',
                        'test_path' => $root . '/tests/Fixtures/basil/InvalidTest/action-contains-unknown-element.yml',
                        'step_name' => 'action contains unknown element',
                        'statement' => 'click $elements.unknown_element_name',
                    ]
                ),
            ],
            'test imports step, step contains action with unknown element' => [
                'input' => [
                    '--source' =>
                        $root . '/tests/Fixtures/basil/InvalidTest/import-action-containing-unknown-element.yml',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => ErrorOutputFactory::CODE_LOADER_UNKNOWN_ELEMENT,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/import-action-containing-unknown-element.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unknown element "unknown_element_name"',
                    ErrorOutputFactory::CODE_LOADER_UNKNOWN_ELEMENT,
                    [
                        'element_name' => 'unknown_element_name',
                        'test_path' => $root .
                            '/tests/Fixtures/basil/InvalidTest/import-action-containing-unknown-element.yml',
                        'step_name' => 'use action_contains_unknown_element',
                        'statement' => 'click $elements.unknown_element_name',
                    ]
                ),
            ],
        ];
    }
}
