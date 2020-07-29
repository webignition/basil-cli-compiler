<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use phpmock\mockery\PHPMockery;
use webignition\BasilCliCompiler\Services\ConfigurationValidator;
use webignition\BasilCliCompiler\Tests\Services\ProjectRootPathProvider;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\ErrorOutput;

class ConfigurationValidatorTest extends AbstractBaseTest
{
    private ConfigurationValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new ConfigurationValidator();
    }

    public function testDeriveInvalidConfigurationErrorCodeSourceNotReadable()
    {
        $root = (new ProjectRootPathProvider())->get();

        $configuration = new Configuration(
            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
            $root . '/tests/build/target',
            AbstractBaseTest::class
        );

        PHPMockery::mock('webignition\BasilCliCompiler\Services', 'is_readable')->andReturn(false);

        self::assertSame(
            ErrorOutput::CODE_COMMAND_CONFIG_SOURCE_INVALID_NOT_READABLE,
            $this->validator->deriveInvalidConfigurationErrorCode($configuration)
        );
    }

    public function testDeriveInvalidConfigurationErrorCodeValidTargetNotWritable(): void
    {
        $root = (new ProjectRootPathProvider())->get();

        $configuration = new Configuration(
            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
            $root . '/tests/build/target',
            AbstractBaseTest::class
        );

        PHPMockery::mock('webignition\BasilCliCompiler\Services', 'is_writable')->andReturn(false);

        self::assertSame(
            ErrorOutput::CODE_COMMAND_CONFIG_TARGET_INVALID_NOT_WRITABLE,
            $this->validator->deriveInvalidConfigurationErrorCode($configuration)
        );
    }
}
