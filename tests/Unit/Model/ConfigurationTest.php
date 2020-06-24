<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Model;

use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;

class ConfigurationTest extends AbstractBaseTest
{
    private const SOURCE = 'test.yml';
    private const TARGET = 'build';
    private const BASE_CLASS = 'BaseClass';

    private Configuration $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new Configuration(self::SOURCE, self::TARGET, self::BASE_CLASS);
    }

    public function testGetSource()
    {
        self::assertSame(self::SOURCE, $this->configuration->getSource());
    }

    public function testGetTarget()
    {
        self::assertSame(self::TARGET, $this->configuration->getTarget());
    }

    public function testGetBaseClass()
    {
        self::assertSame(self::BASE_CLASS, $this->configuration->getBaseClass());
    }

    public function testJsonSerialize()
    {
        self::assertSame(
            [
                'source' => self::SOURCE,
                'target' => self::TARGET,
                'base-class' => self::BASE_CLASS,
            ],
            $this->configuration->jsonSerialize()
        );
    }

    /**
     * @dataProvider fromArrayDataProvider
     *
     * @param array<mixed> $data
     * @param Configuration $expectedConfiguration
     */
    public function testFromArray(array $data, Configuration $expectedConfiguration)
    {
        self::assertEquals($expectedConfiguration, Configuration::fromArray($data));
    }

    public function fromArrayDataProvider(): array
    {
        return [
            'empty' => [
                'data' => [],
                'expectedConfiguration' => new Configuration('', '', '')
            ],
            'source only' => [
                'data' => [
                    'source' => self::SOURCE,
                ],
                'expectedConfiguration' => new Configuration(self::SOURCE, '', '')
            ],
            'target only' => [
                'data' => [
                    'target' => self::TARGET,
                ],
                'expectedConfiguration' => new Configuration('', self::TARGET, '')
            ],
            'base-class only' => [
                'data' => [
                    'base-class' => self::BASE_CLASS,
                ],
                'expectedConfiguration' => new Configuration('', '', self::BASE_CLASS)
            ],
            'populated' => [
                'data' => [
                    'source' => self::SOURCE,
                    'target' => self::TARGET,
                    'base-class' => self::BASE_CLASS,
                ],
                'expectedConfiguration' => new Configuration(self::SOURCE, self::TARGET, self::BASE_CLASS)
            ],
        ];
    }
}
