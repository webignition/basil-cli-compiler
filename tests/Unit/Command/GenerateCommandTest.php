<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Command;

use Symfony\Component\Console\Tester\CommandTester;
use webignition\BaseBasilTestCase\AbstractBaseTest as BasilBaseTest;
use webignition\BasilCliCompiler\Command\GenerateCommand;
use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\ErrorOutput;
use webignition\BasilCliCompiler\Model\GeneratedTestOutput;
use webignition\BasilCliCompiler\Model\SuccessOutput;
use webignition\BasilCliCompiler\Services\ConfigurationFactory;
use webignition\BasilCliCompiler\Services\ConfigurationValidator;
use webignition\BasilCliCompiler\Services\ErrorOutputFactory;
use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;
use webignition\BasilCliCompiler\Services\TestWriter;
use webignition\BasilCliCompiler\Services\ValidatorInvalidResultSerializer;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;
use webignition\BasilLoader\SourceLoader;
use webignition\BasilModels\Test\TestInterface;

class GenerateCommandTest extends AbstractBaseTest
{
    /**
     * @dataProvider runSuccessDataProvider
     *
     * @param array<string, string> $input
     * @param TestWriter $testWriter
     * @param SuccessOutput $expectedCommandOutput
     *
     */
    public function testRunSuccess(
        array $input,
        ConfigurationFactory $configurationFactory,
        TestWriter $testWriter,
        SuccessOutput $expectedCommandOutput
    ): void {
        $configurationValidator = \Mockery::mock(ConfigurationValidator::class);
        $configurationValidator
            ->shouldReceive('isValid')
            ->andReturnTrue();

        $command = $this->createCommand($configurationFactory, $configurationValidator, $testWriter);

        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute($input);
        self::assertSame(0, $exitCode);

        $output = $commandTester->getDisplay();
        $commandOutput = SuccessOutput::fromJson($output);
        self::assertEquals($expectedCommandOutput, $commandOutput);
    }

    public function runSuccessDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        return [
            'default' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    '--target' => 'tests/build/target',
                ],
                'configurationFactory' => $this->createConfigurationFactory(
                    [
                         'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                         'tests/build/target',
                        BasilBaseTest::class
                    ],
                    new Configuration(
                        $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                        $root . '/tests/build/target',
                        BasilBaseTest::class
                    )
                ),
                'testGenerator' => $this->createTestWriter($this->createTestGeneratorAndReturnUsingCallable([
                    $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml' => [
                        'expectedFullyQualifiedBaseClass' => BasilBaseTest::class,
                        'expectedTarget' => $root . '/tests/build/target',
                        'generatedTestOutput' => new GeneratedTestOutput(
                            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                            'ExampleComVerifyOpenLiteralTest.php'
                        ),
                    ],
                ])),
                'expectedCommandOutput' => new SuccessOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                        $root . '/tests/build/target',
                        BasilBaseTest::class
                    ),
                    [
                        new GeneratedTestOutput(
                            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                            'ExampleComVerifyOpenLiteralTest.php'
                        )
                    ]
                ),
            ],
        ];
    }

    /**
     * @param array<string, array<string, mixed>> $generatedTestOutputExpectations
     *
     * @return callable
     */
    private function createTestGeneratorAndReturnUsingCallable(array $generatedTestOutputExpectations): callable
    {
        return function (
            TestInterface $test,
            string $fullyQualifiedBaseClass,
            string $target
        ) use ($generatedTestOutputExpectations) {
            $instanceData = $generatedTestOutputExpectations[$test->getPath()] ?? null;
            if (null === $instanceData) {
                return null;
            }

            $expectedFullyQualifiedBaseClass = $instanceData['expectedFullyQualifiedBaseClass'] ?? null;
            self::assertSame($expectedFullyQualifiedBaseClass, $fullyQualifiedBaseClass);

            $expectedTarget = $instanceData['expectedTarget'] ?? null;
            self::assertSame($expectedTarget, $target);

            return $instanceData['generatedTestOutput'];
        };
    }

    /**
     * @param array<string, string> $input
     * @param int $validationErrorCode
     * @param ErrorOutput $expectedCommandOutput
     *
     * @dataProvider runFailureDataProvider
     */
    public function testRunFailure(
        array $input,
        ConfigurationFactory $configurationFactory,
        ConfigurationValidator $configurationValidator,
        int $validationErrorCode,
        ErrorOutput $expectedCommandOutput
    ): void {
        $command = $this->createCommand(
            $configurationFactory,
            $configurationValidator,
            \Mockery::mock(TestWriter::class)
        );

        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute($input);
        self::assertSame($validationErrorCode, $exitCode);

        $output = $commandTester->getDisplay();

        $commandOutput = ErrorOutput::fromJson($output);
        self::assertEquals($expectedCommandOutput, $commandOutput);
    }

    public function runFailureDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        $emptySourceConfiguration = new Configuration(
            '',
            $root . '/tests/build/target',
            BasilBaseTest::class
        );

        $emptyTargetConfiguration = new Configuration(
            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
            '',
            BasilBaseTest::class
        );

        $invalidConfiguration = new Configuration(
            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
            $root . '/tests/build/target',
            'NonExistentBaseClass'
        );

        return [
            'source empty' => [
                'input' => [
                    '--source' => '',
                    '--target' => 'tests/build/target',
                ],
                'configurationFactory' => $this->createConfigurationFactory(
                    [
                        '',
                        'tests/build/target',
                        BasilBaseTest::class,
                    ],
                    $emptySourceConfiguration
                ),
                'configurationValidator' => \Mockery::mock(ConfigurationValidator::class),
                'validationErrorCode' => ErrorOutput::CODE_COMMAND_CONFIG_SOURCE_EMPTY,
                'expectedCommandOutput' => new ErrorOutput(
                    $emptySourceConfiguration,
                    'source empty; call with --source=SOURCE',
                    ErrorOutput::CODE_COMMAND_CONFIG_SOURCE_EMPTY
                ),
            ],
            'target empty' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    '--target' => '',
                ],
                'configurationFactory' => $this->createConfigurationFactory(
                    [
                        'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                        '',
                        BasilBaseTest::class,
                    ],
                    $emptyTargetConfiguration
                ),
                'configurationValidator' => \Mockery::mock(ConfigurationValidator::class),
                'validationErrorCode' => ErrorOutput::CODE_COMMAND_CONFIG_TARGET_EMPTY,
                'expectedCommandOutput' => new ErrorOutput(
                    $emptyTargetConfiguration,
                    'target empty; call with --target=TARGET',
                    ErrorOutput::CODE_COMMAND_CONFIG_TARGET_EMPTY
                ),
            ],
            'invalid configuration: source does not exist' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    '--target' => 'tests/build/target',
                    '--base-class' => 'NonExistentBaseClass',
                ],
                'configurationFactory' => $this->createConfigurationFactory(
                    [
                        'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                        'tests/build/target',
                        'NonExistentBaseClass',
                    ],
                    $invalidConfiguration
                ),
                'configurationValidator' => $this->createGenerateCommandConfigurationValidator(
                    $invalidConfiguration,
                    ErrorOutput::CODE_COMMAND_CONFIG_BASE_CLASS_DOES_NOT_EXIST
                ),
                'validationErrorCode' => ErrorOutput::CODE_COMMAND_CONFIG_BASE_CLASS_DOES_NOT_EXIST,
                'expectedCommandOutput' => new ErrorOutput(
                    $invalidConfiguration,
                    'base class invalid: does not exist',
                    ErrorOutput::CODE_COMMAND_CONFIG_BASE_CLASS_DOES_NOT_EXIST
                ),
            ],
        ];
    }

    private function createCommand(
        ConfigurationFactory $configurationFactory,
        ConfigurationValidator $configurationValidator,
        TestWriter $testWriter
    ): GenerateCommand {
        return new GenerateCommand(
            SourceLoader::createLoader(),
            $testWriter,
            $configurationFactory,
            $configurationValidator,
            new ErrorOutputFactory($configurationValidator, new ValidatorInvalidResultSerializer())
        );
    }

    private function createTestWriter(callable $andReturnUsingCallable): TestWriter
    {
        $testGenerator = \Mockery::mock(TestWriter::class);

        $testGenerator
            ->shouldReceive('generate')
            ->andReturnUsing($andReturnUsingCallable);

        return $testGenerator;
    }

    /**
     * @param array<mixed> $args
     * @param Configuration $configuration
     *
     * @return ConfigurationFactory
     */
    private function createConfigurationFactory(array $args, Configuration $configuration): ConfigurationFactory
    {
        $factory = \Mockery::mock(ConfigurationFactory::class);

        $factory
            ->shouldReceive('create')
            ->withArgs($args)
            ->andReturn($configuration);

        return $factory;
    }

    private function createGenerateCommandConfigurationValidator(
        Configuration $expectedConfiguration,
        int $errorCode
    ): ConfigurationValidator {
        $validator = \Mockery::mock(ConfigurationValidator::class);

        $validator
            ->shouldReceive('isValid')
            ->withArgs(function (Configuration $configuration) use ($expectedConfiguration) {
                self::assertEquals($expectedConfiguration, $configuration);

                return true;
            })
            ->andReturnFalse();

        $validator
            ->shouldReceive('deriveInvalidConfigurationErrorCode')
            ->withArgs(function (Configuration $configuration) use ($expectedConfiguration) {
                self::assertEquals($expectedConfiguration, $configuration);

                return true;
            })
            ->andReturn($errorCode);

        return $validator;
    }
}
