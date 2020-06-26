<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\ErrorOutput;
use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;

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
//
//    public function runUnknownTestDataProvider(): array
//    {
//        $root = (new ProjectRootPathProvider())->get();
//
//        return [
//            'test suite imports test that does not exist' => [
//                'input' => [
//                    '--source' => 'tests/Fixtures/basil/InvalidTestSuite/imports-non-existent-test.yml',
//                    '--target' => 'tests/build/target',
//                ],
//                'expectedExitCode' => ErrorOutput::CODE_LOADER_UNKNOWN_TEST,
//                'expectedCommandOutput' => new ErrorOutput(
//                    new Configuration(
//                        $root . '/tests/Fixtures/basil/InvalidTestSuite/imports-non-existent-test.yml',
//                        $root . '/tests/build/target',
//                        AbstractBaseTest::class
//                    ),
//                    'Unknown test "' . $root . '/tests/Fixtures/basil/Test/non-existent.yml"',
//                    ErrorOutput::CODE_LOADER_UNKNOWN_TEST,
//                    [
//                        'import_name' => $root . '/tests/Fixtures/basil/Test/non-existent.yml',
//                    ]
//                ),
//            ],
//        ];
//    }
//
//    public function runUnresolvedPlaceholderDataProvider(): array
//    {
//        $root = (new ProjectRootPathProvider())->get();
//
//        return [
//            'placeholder CLIENT is not defined' => [
//                'input' => [
//                    '--source' => 'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
//                    '--target' => 'tests/build/target',
//                ],
//                'expectedExitCode' => ErrorOutput::CODE_GENERATOR_UNRESOLVED_PLACEHOLDER,
//                'expectedCommandOutput' => new ErrorOutput(
//                    new Configuration(
//                        $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
//                        $root . '/tests/build/target',
//                        AbstractBaseTest::class
//                    ),
//                    'Unresolved placeholder "CLIENT" in content ' .
//                    '"{{ CLIENT }}->request(\'GET\', \'https://example.com/\');"',
//                    ErrorOutput::CODE_GENERATOR_UNRESOLVED_PLACEHOLDER,
//                    [
//                        'placeholder' => 'CLIENT',
//                        'content' => '{{ CLIENT }}->request(\'GET\', \'https://example.com/\');',
//                    ]
//                ),
//                'initializer' => function (GenerateCommandTest $generateCommandTest) {
//                    $mockExternalVariableIdentifiers = \Mockery::mock(ExternalVariableIdentifiers::class);
//                    $mockExternalVariableIdentifiers
//                        ->shouldReceive('get')
//                        ->andReturn([]);
//
//                    $this->mockTestWriterCompilerExternalVariableIdentifiers(
//                        $generateCommandTest->command,
//                        $mockExternalVariableIdentifiers
//                    );
//                }
//            ],
//        ];
//    }
//
//
//    private function mockTestWriterCompilerExternalVariableIdentifiers(
//        GenerateCommand $command,
//        ExternalVariableIdentifiers $updatedExternalVariableIdentifiers
//    ): void {
//        $testWriter = ObjectReflector::getProperty($command, 'testWriter');
//        $compiler = ObjectReflector::getProperty($testWriter, 'compiler');
//
//        ObjectReflector::setProperty(
//            $compiler,
//            Compiler::class,
//            'externalVariableIdentifiers',
//            $updatedExternalVariableIdentifiers
//        );
//
//        ObjectReflector::setProperty(
//            $testWriter,
//            TestWriter::class,
//            'compiler',
//            $compiler
//        );
//
//        ObjectReflector::setProperty(
//            $command,
//            GenerateCommand::class,
//            'testWriter',
//            $testWriter
//        );
//    }
}
