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
    ) {
        self::assertEquals(
            $expectedOutput,
            $this->factory->createFromInvalidConfiguration(
                $configuration,
                $configurationValidationState
            )
        );
    }

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

        return [
            'source not readable' => [
                'configuration' => $configurationSourceNotReadable,
                'configurationValidationState' => Configuration::VALIDATION_STATE_SOURCE_NOT_READABLE,
                'expectedOutput' => new ErrorOutput(
                    $configurationSourceNotReadable,
                    'source invalid; file is not readable',
                    ErrorOutput::CODE_COMMAND_CONFIG_SOURCE_INVALID_NOT_READABLE
                ),
            ],
            'target not writable' => [
                'configuration' => $configurationTargetNotWritable,
                'configurationValidationState' => Configuration::VALIDATION_STATE_TARGET_NOT_WRITABLE,
                'expectedOutput' => new ErrorOutput(
                    $configurationTargetNotWritable,
                    'target invalid; directory is not writable',
                    ErrorOutput::CODE_COMMAND_CONFIG_TARGET_INVALID_NOT_WRITABLE
                ),
            ],
            'target not a directory' => [
                'configuration' => $configurationTargetNotDirectory,
                'configurationValidationState' => Configuration::VALIDATION_STATE_TARGET_NOT_DIRECTORY,
                'expectedOutput' => new ErrorOutput(
                    $configurationTargetNotDirectory,
                    'target invalid; is not a directory (is it a file?)',
                    ErrorOutput::CODE_COMMAND_CONFIG_TARGET_INVALID_NOT_A_DIRECTORY
                ),
            ],
        ];
    }
}
