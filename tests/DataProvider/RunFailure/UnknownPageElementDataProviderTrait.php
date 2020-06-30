<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\ErrorOutput;
use webignition\BasilCliCompiler\Tests\Services\ProjectRootPathProvider;

trait UnknownPageElementDataProviderTrait
{
    public function unknownPageElementDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        return [
            'test declares step, step contains action using unknown page element' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTest/action-contains-unknown-page-element.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNKNOWN_PAGE_ELEMENT,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/action-contains-unknown-page-element.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unknown page element "unknown_element" in page "page_import_name"',
                    ErrorOutput::CODE_LOADER_UNKNOWN_PAGE_ELEMENT,
                    [
                        'import_name' => 'page_import_name',
                        'element_name' => 'unknown_element',
                        'test_path' =>
                            $root . '/tests/Fixtures/basil/InvalidTest/action-contains-unknown-page-element.yml',
                        'step_name' => 'action contains unknown page element',
                        'statement' => 'click $page_import_name.elements.unknown_element'
                    ]
                ),
            ],
            'test imports step, test passes step unknown page element' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTest/imports-test-passes-unknown-element.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNKNOWN_PAGE_ELEMENT,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTest/imports-test-passes-unknown-element.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unknown page element "unknown_element" in page "page_import_name"',
                    ErrorOutput::CODE_LOADER_UNKNOWN_PAGE_ELEMENT,
                    [
                        'import_name' => 'page_import_name',
                        'element_name' => 'unknown_element',
                        'test_path' =>
                            $root . '/tests/Fixtures/basil/InvalidTest/imports-test-passes-unknown-element.yml',
                        'step_name' => 'action contains unknown page element',
                        'statement' => ''
                    ]
                ),
            ],
            'test suite imports test declaring step, step contains action using unknown page element' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/InvalidTestSuite/' .
                        'imports-test-declaring-action-containing-unknown-page-element.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNKNOWN_PAGE_ELEMENT,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/InvalidTestSuite/' .
                        'imports-test-declaring-action-containing-unknown-page-element.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unknown page element "unknown_element" in page "page_import_name"',
                    ErrorOutput::CODE_LOADER_UNKNOWN_PAGE_ELEMENT,
                    [
                        'import_name' => 'page_import_name',
                        'element_name' => 'unknown_element',
                        'test_path' =>
                            $root . '/tests/Fixtures/basil/InvalidTest/action-contains-unknown-page-element.yml',
                        'step_name' => 'action contains unknown page element',
                        'statement' => 'click $page_import_name.elements.unknown_element'
                    ]
                ),
            ],
        ];
    }
}
