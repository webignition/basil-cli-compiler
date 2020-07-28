<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Model;

use webignition\BasilCliCompiler\Model\GeneratedTestOutput;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;
use webignition\BasilModels\Test\Configuration;
use webignition\BasilModels\Test\ConfigurationInterface;

class GeneratedTestOutputTest extends AbstractBaseTest
{
    private const SOURCE = 'test.yml';
    private const TARGET = 'GeneratedTest.php';

    private GeneratedTestOutput $output;
    private ConfigurationInterface $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new Configuration('chrome', 'http://example.com');
        $this->output = new GeneratedTestOutput($this->configuration, self::SOURCE, self::TARGET);
    }

    public function testGetConfiguration()
    {
        self::assertSame($this->configuration, $this->output->getConfiguration());
    }

    public function testGetSource()
    {
        self::assertSame(self::SOURCE, $this->output->getSource());
    }

    public function testGetTarget()
    {
        self::assertSame(self::TARGET, $this->output->getTarget());
    }

    public function testGetData()
    {
        self::assertSame(
            [
                'configuration' => [
                    'browser' => 'chrome',
                    'url' => 'http://example.com',
                ],
                'source' => self::SOURCE,
                'target' => self::TARGET,
            ],
            $this->output->getData()
        );
    }

    public function testFromArray()
    {
        self::assertEquals(
            new GeneratedTestOutput($this->configuration, self::SOURCE, self::TARGET),
            GeneratedTestOutput::fromArray([
                'configuration' => [
                    'browser' => 'chrome',
                    'url' => 'http://example.com',
                ],
                'source' => self::SOURCE,
                'target' => self::TARGET,
            ])
        );
    }
}
