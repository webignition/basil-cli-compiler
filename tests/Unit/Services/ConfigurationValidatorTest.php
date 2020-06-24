<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use phpmock\mockery\PHPMockery;
use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\ErrorOutput;
use webignition\BasilCliCompiler\Services\ConfigurationValidator;
use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;

class ConfigurationValidatorTest extends AbstractBaseTest
{
    private ConfigurationValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new ConfigurationValidator();
    }

    public function testIsValidSourceNotReadable(): void
    {
        $root = (new ProjectRootPathProvider())->get();

        $configuration = new Configuration(
            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
            $root . '/tests/build/target',
            AbstractBaseTest::class
        );

        PHPMockery::mock('webignition\BasilCliCompiler\Services', 'is_readable')->andReturn(false);

        self::assertFalse($this->validator->isValid($configuration));
    }

    public function testIsValidTargetNotWritable(): void
    {
        $root = (new ProjectRootPathProvider())->get();

        $configuration = new Configuration(
            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
            $root . '/tests/build/target',
            AbstractBaseTest::class
        );

        PHPMockery::mock('webignition\BasilCliCompiler\Services', 'is_writable')->andReturn(false);

        self::assertFalse($this->validator->isValid($configuration));
    }

    /**
     * @dataProvider isValidDataProvider
     */
    public function testIsValid(Configuration $configuration, bool $expectedIsValid)
    {
        self::assertSame($expectedIsValid, $this->validator->isValid($configuration));
    }

    public function isValidDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        $source = $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml';
        $target = $root . '/tests/build/target';

        return [
            'valid' => [
                'configuration' => new Configuration($source, $target, AbstractBaseTest::class),
                'expectedIsValid' => true,
            ],
            'invalid: source is empty' => [
                'configuration' => new Configuration('', $target, AbstractBaseTest::class),
                'expectedIsValid' => false,
            ],
            'invalid: target is empty' => [
                'configuration' => new Configuration($source, '', AbstractBaseTest::class),
                'expectedIsValid' => false,
            ],
            'invalid: target is not a directory, is a file' => [
                'configuration' => new Configuration($source, $source, AbstractBaseTest::class),
                'expectedIsValid' => false,
            ],
            'invalid: base class does not exist' => [
                'configuration' => new Configuration($source, $target, 'Foo'),
                'expectedIsValid' => false,
            ],
        ];
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
