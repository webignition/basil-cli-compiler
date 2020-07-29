<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use webignition\BasilCliCompiler\Services\ConfigurationFactory;
use webignition\BasilCliCompiler\Tests\Services\ProjectRootPathProvider;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\ConfigurationInterface;

class ConfigurationFactoryTest extends AbstractBaseTest
{
    private ConfigurationFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ConfigurationFactory(
            (new ProjectRootPathProvider())->get()
        );
    }

    /**
     * @dataProvider createFromTypedInputSuccessDataProvider
     */
    public function testCreate(
        string $rawSource,
        string $rawTarget,
        string $baseClass,
        ConfigurationInterface $expectedConfiguration
    ) {
        self::assertEquals($expectedConfiguration, $this->factory->create($rawSource, $rawTarget, $baseClass));
    }

    public function createFromTypedInputSuccessDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        return [
            'source and target resolve to absolute paths' => [
                'rawSource' => 'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                'rawTarget' => 'tests/build/target',
                'baseClass' => AbstractBaseTest::class,
                'expectedConfiguration' => new Configuration(
                    $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    $root . '/tests/build/target',
                    AbstractBaseTest::class
                ),
            ],
        ];
    }
}
