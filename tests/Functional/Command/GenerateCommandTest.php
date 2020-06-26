<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Functional\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Command\GenerateCommand;
use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\ErrorOutput;
use webignition\BasilCliCompiler\Model\SuccessOutput;
use webignition\BasilCliCompiler\Services\CommandFactory;
use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;
use webignition\BasilCliCompiler\Services\TestWriter;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\CircularStepImportDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\EmptyTestDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\InvalidPageDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\InvalidTestDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\NonLoadableDataDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\NonRetrievableImportDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\ParseExceptionDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\UnknownElementDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\UnknownItemDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\UnknownPageElementDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\UnknownTestDataProviderTrait;
use webignition\BasilCompilableSourceFactory\ClassDefinitionFactory;
use webignition\BasilCompilableSourceFactory\ClassNameFactory;
use webignition\BasilCompilableSourceFactory\Exception\UnsupportedContentException;
use webignition\BasilCompilableSourceFactory\Exception\UnsupportedStatementException;
use webignition\BasilCompilableSourceFactory\Exception\UnsupportedStepException;
use webignition\BasilCompiler\Compiler;
use webignition\BasilCompiler\ExternalVariableIdentifiers;
use webignition\BasilModels\Step\Step;
use webignition\BasilModels\Test\TestInterface;
use webignition\BasilParser\ActionParser;
use webignition\BasilParser\AssertionParser;
use webignition\ObjectReflector\ObjectReflector;

class GenerateCommandTest extends \PHPUnit\Framework\TestCase
{
    use NonLoadableDataDataProviderTrait;
    use CircularStepImportDataProviderTrait;
    use EmptyTestDataProviderTrait;
    use InvalidPageDataProviderTrait;
    use InvalidTestDataProviderTrait;
    use NonRetrievableImportDataProviderTrait;
    use ParseExceptionDataProviderTrait;
    use UnknownElementDataProviderTrait;
    use UnknownItemDataProviderTrait;
    use UnknownPageElementDataProviderTrait;
    use UnknownTestDataProviderTrait;

    private GenerateCommand $command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->command = CommandFactory::createGenerateCommand();
    }

    /**
     * @param array<string, string> $input
     * @param array<string, string> $generatedCodeClassNames
     * @param array<string> $expectedGeneratedTestOutputSources
     * @param array<string, string> $expectedGeneratedCode
     *
     * @dataProvider runSuccessDataProvider
     */
    public function testRunSuccess(
        array $input,
        array $generatedCodeClassNames,
        array $expectedGeneratedTestOutputSources,
        array $expectedGeneratedCode
    ) {
        $this->mockClassNameFactory($generatedCodeClassNames);

        $output = new BufferedOutput();

        $exitCode = $this->command->run(new ArrayInput($input), $output);
        self::assertSame(0, $exitCode);

        $commandOutput = SuccessOutput::fromJson($output->fetch());

        $outputData = $commandOutput->getOutput();
        self::assertCount(count($expectedGeneratedTestOutputSources), $outputData);

        $generatedTestOutputIndex = 0;
        $generatedTestsToRemove = [];
        foreach ($outputData as $generatedTestOutput) {
            $expectedGeneratedTestOutputSource = $expectedGeneratedTestOutputSources[$generatedTestOutputIndex] ?? null;

            $generatedTestOutputSource = $generatedTestOutput->getSource();
            self::assertSame($expectedGeneratedTestOutputSource, $generatedTestOutputSource);

            $expectedGeneratedCodeClassName = $generatedCodeClassNames[$generatedTestOutputSource] ?? '';
            self::assertSame($expectedGeneratedCodeClassName . '.php', $generatedTestOutput->getTarget());

            $commandOutputConfiguration = $commandOutput->getConfiguration();
            $commandOutputTarget = $commandOutputConfiguration->getTarget();

            $expectedCodePath = $commandOutputTarget . '/' . $generatedTestOutput->getTarget();

            self::assertFileExists($expectedCodePath);
            self::assertFileIsReadable($expectedCodePath);

            self::assertEquals(
                $expectedGeneratedCode[$generatedTestOutput->getSource()],
                file_get_contents($expectedCodePath)
            );

            $generatedTestsToRemove[] = $expectedCodePath;
            $generatedTestOutputIndex++;
        }

        $generatedTestsToRemove = array_unique($generatedTestsToRemove);

        foreach ($generatedTestsToRemove as $path) {
            self::assertFileExists($path);
            self::assertFileIsReadable($path);

            unlink($path);
        }
    }

    public function runSuccessDataProvider(): array
    {
        return [
            'single test' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    '--target' => 'tests/build/target',
                ],
                'generatedCodeClassNames' => [
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml' =>
                        'ExampleComVerifyOpenLiteralTest',
                ],
                'expectedGeneratedTestOutputSources' => [
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                ],
                'expectedGeneratedCode' => [
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/ExampleComVerifyOpenLiteralTest.php')
                ],
            ],
            'test suite' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/TestSuite/example.com-all.yml',
                    '--target' => 'tests/build/target',
                ],
                'generatedCodeClassNames' => [
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml' =>
                        'ExampleComVerifyOpenLiteralTest',
                    'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml' =>
                        'ExampleComImportVerifyOpenLiteralTest',
                    'tests/Fixtures/basil/Test/example.com.follow-more-information.yml' =>
                        'ExampleComFollowMoreInformationTest',
                ],
                'expectedGeneratedTestOutputSources' => [
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml',
                    'tests/Fixtures/basil/Test/example.com.follow-more-information.yml',
                ],
                'expectedGeneratedCode' => [
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/ExampleComVerifyOpenLiteralTest.php'),
                    'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/ExampleComImportVerifyOpenLiteralTest.php'),
                    'tests/Fixtures/basil/Test/example.com.follow-more-information.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/ExampleComFollowMoreInformationTest.php')
                ],
            ],
            'collection of tests by directory' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/Test',
                    '--target' => 'tests/build/target',
                ],
                'generatedCodeClassNames' => [
                    'tests/Fixtures/basil/Test/example.com.follow-more-information.yml' =>
                        'ExampleComFollowMoreInformationTest',
                    'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml' =>
                        'ExampleComImportVerifyOpenLiteralTest',
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml' =>
                        'ExampleComVerifyOpenLiteralTest',
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal-data-sets.yml' =>
                        'ExampleComVerifyOpenLiteralDataSetsTest',
                ],
                'expectedGeneratedTestOutputSources' => [
                    'tests/Fixtures/basil/Test/example.com.follow-more-information.yml',
                    'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml',
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal-data-sets.yml',
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                ],
                'expectedGeneratedCode' => [
                    'tests/Fixtures/basil/Test/example.com.follow-more-information.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/ExampleComFollowMoreInformationTest.php'),
                    'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/ExampleComImportVerifyOpenLiteralTest.php'),
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal-data-sets.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/ExampleComVerifyOpenLiteralDataSetsTest.php'),
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/ExampleComVerifyOpenLiteralTest.php'),
                ],
            ],
            'collection of test suites by directory' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/TestSuite',
                    '--target' => 'tests/build/target',
                ],
                'generatedCodeClassNames' => [
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml' =>
                        'ExampleComVerifyOpenLiteralTest',
                    'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml' =>
                        'ExampleComImportVerifyOpenLiteralTest',
                    'tests/Fixtures/basil/Test/example.com.follow-more-information.yml' =>
                        'ExampleComFollowMoreInformationTest',
                ],
                'expectedGeneratedTestOutputSources' => [
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml',
                    'tests/Fixtures/basil/Test/example.com.follow-more-information.yml',
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                ],
                'expectedGeneratedCode' => [
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/ExampleComVerifyOpenLiteralTest.php'),
                    'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/ExampleComImportVerifyOpenLiteralTest.php'),
                    'tests/Fixtures/basil/Test/example.com.follow-more-information.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/ExampleComFollowMoreInformationTest.php'),
                ],
            ],
        ];
    }

    /**
     * @param array<mixed> $input
     * @param int $expectedExitCode
     * @param ErrorOutput $expectedCommandOutput
     *
     * @dataProvider nonLoadableDataDataProvider
     * @dataProvider circularStepImportDataProvider
     * @dataProvider emptyTestDataProvider
     * @dataProvider invalidPageDataProvider
     * @dataProvider invalidTestDataProvider
     * @dataProvider nonRetrievableImportDataProvider
     * @dataProvider parseExceptionDataProvider
     * @dataProvider unknownElementDataProvider
     * @dataProvider unknownItemDataProvider
     * @dataProvider unknownPageElementDataProvider
     * @dataProvider unknownTestDataProvider
     * @dataProvider unresolvedPlaceholderDataProvider
     */
    public function testRunFailure(
        array $input,
        int $expectedExitCode,
        ErrorOutput $expectedCommandOutput,
        ?callable $initializer = null
    ) {
        if (null !== $initializer) {
            $initializer($this, $this->command);
        }

        $output = new BufferedOutput();

        $exitCode = $this->command->run(new ArrayInput($input), $output);
        self::assertSame($expectedExitCode, $exitCode);

        $commandOutput = ErrorOutput::fromJson($output->fetch());

        self::assertEquals($expectedCommandOutput, $commandOutput);
    }

    public function unresolvedPlaceholderDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        return [
            'placeholder CLIENT is not defined' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_GENERATOR_UNRESOLVED_PLACEHOLDER,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unresolved placeholder "CLIENT" in content ' .
                    '"{{ CLIENT }}->request(\'GET\', \'https://example.com/\');"',
                    ErrorOutput::CODE_GENERATOR_UNRESOLVED_PLACEHOLDER,
                    [
                        'placeholder' => 'CLIENT',
                        'content' => '{{ CLIENT }}->request(\'GET\', \'https://example.com/\');',
                    ]
                ),
                'initializer' => function (GenerateCommandTest $generateCommandTest) {
                    $mockExternalVariableIdentifiers = \Mockery::mock(ExternalVariableIdentifiers::class);
                    $mockExternalVariableIdentifiers
                        ->shouldReceive('get')
                        ->andReturn([]);

                    $this->mockTestWriterCompilerExternalVariableIdentifiers(
                        $generateCommandTest->command,
                        $mockExternalVariableIdentifiers
                    );
                }
            ],
        ];
    }

    /**
     * @dataProvider runFailureUnsupportedStepDataProvider
     *
     * @param UnsupportedStepException $unsupportedStepException
     * @param array<mixed> $expectedErrorOutputContext
     */
    public function testRunFailureUnsupportedStepException(
        UnsupportedStepException $unsupportedStepException,
        array $expectedErrorOutputContext
    ) {
        $root = (new ProjectRootPathProvider())->get();

        $input = [
            '--source' => 'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
            '--target' => 'tests/build/target',
        ];

        $testWriter = \Mockery::mock(TestWriter::class);
        $testWriter
            ->shouldReceive('generate')
            ->andThrow($unsupportedStepException);

        $this->mockTestWriter($this->command, $testWriter);

        $output = new BufferedOutput();

        $exitCode = $this->command->run(new ArrayInput($input), $output);
        self::assertSame(ErrorOutput::CODE_GENERATOR_UNSUPPORTED_STEP, $exitCode);

        $expectedCommandOutput = new ErrorOutput(
            new Configuration(
                $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                $root . '/tests/build/target',
                AbstractBaseTest::class
            ),
            'Unsupported step',
            ErrorOutput::CODE_GENERATOR_UNSUPPORTED_STEP,
            $expectedErrorOutputContext
        );

        $commandOutput = ErrorOutput::fromJson($output->fetch());

        self::assertEquals($expectedCommandOutput, $commandOutput);
    }

    public function runFailureUnsupportedStepDataProvider(): array
    {
        $actionParser = ActionParser::create();
        $assertionParser = AssertionParser::create();

        return [
            'click action with attribute identifier' => [
                'unsupportedStepException' => new UnsupportedStepException(
                    new Step(
                        [
                            $actionParser->parse('click $".selector".attribute_name'),
                        ],
                        []
                    ),
                    new UnsupportedStatementException(
                        $actionParser->parse('click $".selector".attribute_name'),
                        new UnsupportedContentException(
                            UnsupportedContentException::TYPE_IDENTIFIER,
                            '$".selector".attribute_name'
                        )
                    )
                ),
                'expectedErrorOutputContext' => [
                    'statement_type' => 'action',
                    'statement' => 'click $".selector".attribute_name',
                    'content_type' => 'identifier',
                    'content' => '$".selector".attribute_name',
                ],
            ],
            'comparison assertion examined value identifier cannot be extracted' => [
                'unsupportedStepException' => new UnsupportedStepException(
                    new Step(
                        [],
                        [
                            $assertionParser->parse('$".selector" is "value"'),
                        ]
                    ),
                    new UnsupportedStatementException(
                        $assertionParser->parse('$".selector" is "value"'),
                        new UnsupportedContentException(
                            UnsupportedContentException::TYPE_IDENTIFIER,
                            '$".selector"'
                        )
                    )
                ),
                'expectedErrorOutputContext' => [
                    'statement_type' => 'assertion',
                    'statement' => '$".selector" is "value"',
                    'content_type' => 'identifier',
                    'content' => '$".selector"',
                ],
            ],
            'comparison assertion examined value is not supported' => [
                'unsupportedStepException' => new UnsupportedStepException(
                    new Step(
                        [],
                        [
                            $assertionParser->parse('$elements.element_name is "value"'),
                        ]
                    ),
                    new UnsupportedStatementException(
                        $assertionParser->parse('$elements.element_name is "value"'),
                        new UnsupportedContentException(
                            UnsupportedContentException::TYPE_VALUE,
                            '$elements.element_name'
                        )
                    )
                ),
                'expectedErrorOutputContext' => [
                    'statement_type' => 'assertion',
                    'statement' => '$elements.element_name is "value"',
                    'content_type' => 'value',
                    'content' => '$elements.element_name',
                ],
            ],
            'unsupported action type' => [
                'unsupportedStepException' => new UnsupportedStepException(
                    new Step(
                        [
                            $actionParser->parse('foo $".selector"'),
                        ],
                        []
                    ),
                    new UnsupportedStatementException(
                        $actionParser->parse('foo $".selector"')
                    )
                ),
                'expectedErrorOutputContext' => [
                    'statement_type' => 'action',
                    'statement' => 'foo $".selector"',
                ],
            ],
        ];
    }

    private function mockTestWriter(GenerateCommand $command, TestWriter $mockTestWriter): void
    {
        ObjectReflector::setProperty(
            $command,
            GenerateCommand::class,
            'testWriter',
            $mockTestWriter
        );
    }

    private function mockTestWriterCompilerExternalVariableIdentifiers(
        GenerateCommand $command,
        ExternalVariableIdentifiers $updatedExternalVariableIdentifiers
    ): void {
        $testWriter = ObjectReflector::getProperty($command, 'testWriter');
        $compiler = ObjectReflector::getProperty($testWriter, 'compiler');

        ObjectReflector::setProperty(
            $compiler,
            Compiler::class,
            'externalVariableIdentifiers',
            $updatedExternalVariableIdentifiers
        );

        ObjectReflector::setProperty(
            $testWriter,
            TestWriter::class,
            'compiler',
            $compiler
        );

        ObjectReflector::setProperty(
            $command,
            GenerateCommand::class,
            'testWriter',
            $testWriter
        );
    }

    /**
     * @param array<string, string> $classNames
     */
    private function mockClassNameFactory(array $classNames): void
    {
        $testWriter = ObjectReflector::getProperty($this->command, 'testWriter');

        $classNameFactory = \Mockery::mock(ClassNameFactory::class);
        $classNameFactory
            ->shouldReceive('create')
            ->andReturnUsing(function (TestInterface $test) use ($classNames) {
                return $classNames[$test->getPath()] ?? null;
            });

        /** @var ClassDefinitionFactory $classDefinitionFactory */
        $classDefinitionFactory = ObjectReflector::getProperty($testWriter, 'classDefinitionFactory');
        ObjectReflector::setProperty(
            $classDefinitionFactory,
            ClassDefinitionFactory::class,
            'classNameFactory',
            $classNameFactory
        );

        ObjectReflector::setProperty(
            $testWriter,
            TestWriter::class,
            'classDefinitionFactory',
            $classDefinitionFactory
        );

        ObjectReflector::setProperty(
            $this->command,
            GenerateCommand::class,
            'testWriter',
            $testWriter
        );
    }
}
