<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Functional\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Yaml\Yaml;
use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Command\GenerateCommand;
use webignition\BasilCliCompiler\Model\ExternalVariableIdentifiers;
use webignition\BasilCliCompiler\Services\CommandFactory;
use webignition\BasilCliCompiler\Services\CompiledClassResolver;
use webignition\BasilCliCompiler\Services\Compiler;
use webignition\BasilCliCompiler\Services\ErrorOutputFactory;
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
use webignition\BasilCliCompiler\Tests\DataProvider\RunSuccess\SuccessDataProviderTrait;
use webignition\BasilCliCompiler\Tests\Services\ServiceMocker;
use webignition\BasilCompilableSourceFactory\Exception\UnsupportedContentException;
use webignition\BasilCompilableSourceFactory\Exception\UnsupportedStatementException;
use webignition\BasilCompilableSourceFactory\Exception\UnsupportedStepException;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\ErrorOutput;
use webignition\BasilCompilerModels\ErrorOutputInterface;
use webignition\BasilCompilerModels\SuiteManifest;
use webignition\BasilModels\Step\Step;
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
    use SuccessDataProviderTrait;

    /**
     * @param array<string, string> $input
     * @param int $expectedExitCode
     * @param SuiteManifest $expectedCommandOutput
     * @param string[] $expectedGeneratedCodePaths
     * @param string[] $classNames
     *
     * @dataProvider successDataProvider
     */
    public function testRunSuccess(
        array $input,
        int $expectedExitCode,
        SuiteManifest $expectedCommandOutput,
        array $expectedGeneratedCodePaths,
        array $classNames
    ) {
        $stdout = new BufferedOutput();
        $stderr = new BufferedOutput();

        $command = CommandFactory::createGenerateCommand($stdout, $stderr, $this->createArgvFromInput($input));

        $this->mockClassNameFactoryOnCommand($command, $classNames);

        $exitCode = $command->run(new ArrayInput($input), new NullOutput());
        self::assertSame($expectedExitCode, $exitCode);
        self::assertSame('', $stderr->fetch());

        $suiteManifest = SuiteManifest::fromArray((array) Yaml::parse($stdout->fetch()));
        self::assertEquals($expectedCommandOutput, $suiteManifest);

        $generatedTestsToRemove = [];
        foreach ($suiteManifest->getTestManifests() as $testManifestIndex => $testManifest) {
            $expectedCodePath = $testManifest->getTarget();

            self::assertFileExists($expectedCodePath);
            self::assertFileIsReadable($expectedCodePath);

            $expectedGeneratedCodePath = $expectedGeneratedCodePaths[$testManifestIndex];
            $expectedGeneratedCode = file_get_contents($expectedGeneratedCodePath);
            $generatedCode = file_get_contents($expectedCodePath);

            self::assertSame($expectedGeneratedCode, $generatedCode);

            $generatedTestsToRemove[] = $expectedCodePath;
        }

        $generatedTestsToRemove = array_unique($generatedTestsToRemove);

        foreach ($generatedTestsToRemove as $path) {
            self::assertFileExists($path);
            self::assertFileIsReadable($path);

            unlink($path);
        }
    }

    /**
     * @param array<mixed> $input
     * @param int $expectedExitCode
     * @param ErrorOutputInterface $expectedCommandOutput
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
     * @dataProvider unresolvedPlaceholderDataProvider
     */
    public function testRunFailure(
        array $input,
        int $expectedExitCode,
        ErrorOutputInterface $expectedCommandOutput,
        ?callable $initializer = null
    ) {
        $stdout = new BufferedOutput();
        $stderr = new BufferedOutput();

        $command = CommandFactory::createGenerateCommand($stdout, $stderr, $this->createArgvFromInput($input));

        if (null !== $initializer) {
            $initializer($command);
        }

        $exitCode = $command->run(new ArrayInput($input), new NullOutput());
        self::assertSame($expectedExitCode, $exitCode);
        self::assertSame('', $stdout->fetch());

        $commandOutput = ErrorOutput::fromArray((array) Yaml::parse($stderr->fetch()));

        self::assertEquals($expectedCommandOutput, $commandOutput);
    }

    public function unresolvedPlaceholderDataProvider(): array
    {
        $root = getcwd();

        return [
            'placeholder CLIENT is not defined' => [
                'input' => [
                    '--source' => $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => ErrorOutputFactory::CODE_GENERATOR_UNRESOLVED_PLACEHOLDER,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Unresolved variable "CLIENT" in template ' .
                    '"{{ CLIENT }}->request(\'GET\', \'https://example.com/\');"',
                    ErrorOutputFactory::CODE_GENERATOR_UNRESOLVED_PLACEHOLDER,
                    [
                        'placeholder' => 'CLIENT',
                        'content' => '{{ CLIENT }}->request(\'GET\', \'https://example.com/\');',
                    ]
                ),
                'initializer' => function (GenerateCommand $command) {
                    $mockExternalVariableIdentifiers = \Mockery::mock(ExternalVariableIdentifiers::class);
                    $mockExternalVariableIdentifiers
                        ->shouldReceive('get')
                        ->andReturn([]);

                    $this->mockCompilerCompiledClassResolverExternalVariableIdentifiers(
                        $command,
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
        $root = getcwd();

        $input = [
            '--source' => $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
            '--target' => $root . '/tests/build/target',
        ];

        $compiler = \Mockery::mock(Compiler::class);
        $compiler
            ->shouldReceive('compile')
            ->andThrow($unsupportedStepException);

        $stdout = new BufferedOutput();
        $stderr = new BufferedOutput();

        $command = CommandFactory::createGenerateCommand($stdout, $stderr, $this->createArgvFromInput($input));

        ObjectReflector::setProperty(
            $command,
            GenerateCommand::class,
            'compiler',
            $compiler
        );

        $exitCode = $command->run(new ArrayInput($input), new NullOutput());
        self::assertSame(ErrorOutputFactory::CODE_GENERATOR_UNSUPPORTED_STEP, $exitCode);
        self::assertSame('', $stdout->fetch());

        $expectedCommandOutput = new ErrorOutput(
            new Configuration(
                $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                $root . '/tests/build/target',
                AbstractBaseTest::class
            ),
            'Unsupported step',
            ErrorOutputFactory::CODE_GENERATOR_UNSUPPORTED_STEP,
            $expectedErrorOutputContext
        );

        $commandOutput = ErrorOutput::fromArray((array) Yaml::parse($stderr->fetch()));

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

    private function mockCompilerCompiledClassResolverExternalVariableIdentifiers(
        GenerateCommand $command,
        ExternalVariableIdentifiers $updatedExternalVariableIdentifiers
    ): void {
        $compiledClassResolver = CompiledClassResolver::createResolver($updatedExternalVariableIdentifiers);
        $compiler = ObjectReflector::getProperty($command, 'compiler');

        ObjectReflector::setProperty(
            $compiler,
            Compiler::class,
            'compiledClassResolver',
            $compiledClassResolver
        );

        ObjectReflector::setProperty(
            $command,
            GenerateCommand::class,
            'compiler',
            $compiler
        );
    }

    /**
     * @param array<mixed> $input
     *
     * @return array<mixed>
     */
    private function createArgvFromInput(array $input): array
    {
        $argv = [];
        foreach ($input as $key => $value) {
            $argv[] = $key . '=' . $value;
        }

        return $argv;
    }

    /**
     * @param GenerateCommand $command
     * @param string[] $classNames
     */
    private function mockClassNameFactoryOnCommand(GenerateCommand $command, array $classNames): void
    {
        $serviceMocker = new ServiceMocker();

        $compiler = $serviceMocker->mockClassNameFactoryOnCompiler(
            ObjectReflector::getProperty($command, 'compiler'),
            $classNames
        );

        ObjectReflector::setProperty($command, GenerateCommand::class, 'compiler', $compiler);
    }
}
