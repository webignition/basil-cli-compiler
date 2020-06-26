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
