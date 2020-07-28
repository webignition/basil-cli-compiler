<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Model;

use webignition\BasilCliCompiler\Model\TestManifest;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;
use webignition\BasilModels\Test\Configuration;
use webignition\BasilModels\Test\ConfigurationInterface;

class TestManifestTest extends AbstractBaseTest
{
    private const SOURCE = 'test.yml';
    private const TARGET = 'GeneratedTest.php';

    private TestManifest $manifest;
    private ConfigurationInterface $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new Configuration('chrome', 'http://example.com');
        $this->manifest = new TestManifest($this->configuration, self::SOURCE, self::TARGET);
    }

    public function testGetTarget()
    {
        self::assertSame(self::TARGET, $this->manifest->getTarget());
    }

    public function testGetData()
    {
        self::assertSame(
            [
                'config' => [
                    'browser' => 'chrome',
                    'url' => 'http://example.com',
                ],
                'source' => self::SOURCE,
                'target' => self::TARGET,
            ],
            $this->manifest->getData()
        );
    }

    public function testFromArray()
    {
        self::assertEquals(
            new TestManifest($this->configuration, self::SOURCE, self::TARGET),
            TestManifest::fromArray([
                'config' => [
                    'browser' => 'chrome',
                    'url' => 'http://example.com',
                ],
                'source' => self::SOURCE,
                'target' => self::TARGET,
            ])
        );
    }
}
