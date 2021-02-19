<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use webignition\BasilCliCompiler\Services\ErrorOutputFactory;
use webignition\BasilCliCompiler\Services\ValidatorInvalidResultSerializer;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\ConfigurationInterface;
use webignition\BasilCompilerModels\ErrorOutput;

class ErrorOutputFactoryTest extends AbstractBaseTest
{
    private ErrorOutputFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ErrorOutputFactory(
            new ValidatorInvalidResultSerializer()
        );
    }

    /**
     * @dataProvider createFromInvalidConfigurationDataProvider
     */
    public function testCreateFromInvalidConfiguration(
        ConfigurationInterface $configuration,
        int $configurationValidationState,
        ErrorOutput $expectedOutput
    ): void {
        self::assertEquals(
            $expectedOutput,
            $this->factory->createFromInvalidConfiguration(
                $configuration,
                $configurationValidationState
            )
        );
    }

    /**
     * @return array[]
     */
    public function createFromInvalidConfigurationDataProvider(): array
    {
        $configurationSourceNotReadable = \Mockery::mock(ConfigurationInterface::class);
        $configurationSourceNotReadable
            ->shouldReceive('validate')
            ->andReturn(Configuration::VALIDATION_STATE_SOURCE_NOT_READABLE);

        $configurationTargetNotWritable = \Mockery::mock(ConfigurationInterface::class);
        $configurationTargetNotWritable
            ->shouldReceive('validate')
            ->andReturn(Configuration::VALIDATION_STATE_TARGET_NOT_WRITABLE);

        $configurationTargetNotDirectory = \Mockery::mock(ConfigurationInterface::class);
        $configurationTargetNotDirectory
            ->shouldReceive('validate')
            ->andReturn(Configuration::VALIDATION_STATE_TARGET_NOT_DIRECTORY);

        $configurationSourceNotAbsolute = \Mockery::mock(ConfigurationInterface::class);
        $configurationSourceNotAbsolute
            ->shouldReceive('validate')
            ->andReturn(Configuration::VALIDATION_STATE_SOURCE_NOT_ABSOLUTE);

        $configurationTargetNotAbsolute = \Mockery::mock(ConfigurationInterface::class);
        $configurationTargetNotAbsolute
            ->shouldReceive('validate')
            ->andReturn(Configuration::VALIDATION_STATE_TARGET_NOT_ABSOLUTE);

        $configurationSourceEmpty = \Mockery::mock(ConfigurationInterface::class);
        $configurationSourceEmpty
            ->shouldReceive('validate')
            ->andReturn(Configuration::VALIDATION_STATE_SOURCE_EMPTY);

        $configurationTargetEmpty = \Mockery::mock(ConfigurationInterface::class);
        $configurationTargetEmpty
            ->shouldReceive('validate')
            ->andReturn(Configuration::VALIDATION_STATE_TARGET_EMPTY);

        return [
            'source not readable' => [
                'configuration' => $configurationSourceNotReadable,
                'configurationValidationState' => Configuration::VALIDATION_STATE_SOURCE_NOT_READABLE,
                'expectedOutput' => new ErrorOutput(
                    $configurationSourceNotReadable,
                    'source invalid; file is not readable',
                    ErrorOutputFactory::CODE_COMMAND_CONFIG_SOURCE_INVALID_NOT_READABLE
                ),
            ],
            'target not writable' => [
                'configuration' => $configurationTargetNotWritable,
                'configurationValidationState' => Configuration::VALIDATION_STATE_TARGET_NOT_WRITABLE,
                'expectedOutput' => new ErrorOutput(
                    $configurationTargetNotWritable,
                    'target invalid; directory is not writable',
                    ErrorOutputFactory::CODE_COMMAND_CONFIG_TARGET_INVALID_NOT_WRITABLE
                ),
            ],
            'target not a directory' => [
                'configuration' => $configurationTargetNotDirectory,
                'configurationValidationState' => Configuration::VALIDATION_STATE_TARGET_NOT_DIRECTORY,
                'expectedOutput' => new ErrorOutput(
                    $configurationTargetNotDirectory,
                    'target invalid; is not a directory (is it a file?)',
                    ErrorOutputFactory::CODE_COMMAND_CONFIG_TARGET_INVALID_NOT_A_DIRECTORY
                ),
            ],
            'source not absolute' => [
                'configuration' => $configurationSourceNotAbsolute,
                'configurationValidationState' => Configuration::VALIDATION_STATE_SOURCE_NOT_ABSOLUTE,
                'expectedOutput' => new ErrorOutput(
                    $configurationSourceNotAbsolute,
                    'source invalid: path must be absolute',
                    ErrorOutputFactory::CODE_COMMAND_CONFIG_SOURCE_INVALID_NOT_ABSOLUTE
                ),
            ],
            'target not absolute' => [
                'configuration' => $configurationTargetNotAbsolute,
                'configurationValidationState' => Configuration::VALIDATION_STATE_TARGET_NOT_ABSOLUTE,
                'expectedOutput' => new ErrorOutput(
                    $configurationTargetNotAbsolute,
                    'target invalid: path must be absolute',
                    ErrorOutputFactory::CODE_COMMAND_CONFIG_TARGET_INVALID_NOT_ABSOLUTE
                ),
            ],
            'source empty' => [
                'configuration' => $configurationSourceEmpty,
                'configurationValidationState' => Configuration::VALIDATION_STATE_SOURCE_EMPTY,
                'expectedOutput' => new ErrorOutput(
                    $configurationSourceEmpty,
                    'source empty; call with --source=SOURCE',
                    ErrorOutputFactory::CODE_COMMAND_CONFIG_SOURCE_EMPTY
                ),
            ],
            'target empty' => [
                'configuration' => $configurationTargetEmpty,
                'configurationValidationState' => Configuration::VALIDATION_STATE_TARGET_EMPTY,
                'expectedOutput' => new ErrorOutput(
                    $configurationTargetEmpty,
                    'target empty; call with --target=TARGET',
                    ErrorOutputFactory::CODE_COMMAND_CONFIG_TARGET_EMPTY
                ),
            ],
        ];
    }
}
