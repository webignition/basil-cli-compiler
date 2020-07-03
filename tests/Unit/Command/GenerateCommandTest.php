<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;
use webignition\BaseBasilTestCase\AbstractBaseTest as BasilBaseTest;
use webignition\BasilCliCompiler\Command\GenerateCommand;
use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\ErrorOutput;
use webignition\BasilCliCompiler\Services\Compiler;
use webignition\BasilCliCompiler\Services\ConfigurationFactory;
use webignition\BasilCliCompiler\Services\ConfigurationValidator;
use webignition\BasilCliCompiler\Services\ErrorOutputFactory;
use webignition\BasilCliCompiler\Services\TestWriter;
use webignition\BasilCliCompiler\Services\ValidatorInvalidResultSerializer;
use webignition\BasilCliCompiler\Tests\Services\ProjectRootPathProvider;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;
use webignition\BasilLoader\SourceLoader;

class GenerateCommandTest extends AbstractBaseTest
{
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
            \Mockery::mock(Compiler::class),
            \Mockery::mock(TestWriter::class)
        );

        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute($input);
        self::assertSame($validationErrorCode, $exitCode);

        $output = $commandTester->getDisplay();
        $commandOutput = ErrorOutput::fromArray((array) Yaml::parse($output));
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
        ];
    }

    private function createCommand(
        ConfigurationFactory $configurationFactory,
        ConfigurationValidator $configurationValidator,
        Compiler $compiler,
        TestWriter $testWriter
    ): GenerateCommand {
        return new GenerateCommand(
            SourceLoader::createLoader(),
            $compiler,
            $testWriter,
            $configurationFactory,
            $configurationValidator,
            new ErrorOutputFactory($configurationValidator, new ValidatorInvalidResultSerializer()),
            (new ProjectRootPathProvider())->get()
        );
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
}
