<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Model;

use webignition\BasilCliCompiler\Model\GeneratedTestOutput;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;

class GeneratedTestOutputTest extends AbstractBaseTest
{
    private const SOURCE = 'test.yml';
    private const TARGET = 'GeneratedTest.php';

    private GeneratedTestOutput $output;

    protected function setUp(): void
    {
        parent::setUp();

        $this->output = new GeneratedTestOutput(self::SOURCE, self::TARGET);
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
                'source' => self::SOURCE,
                'target' => self::TARGET,
            ],
            $this->output->getData()
        );
    }

    public function testFromArray()
    {
        self::assertEquals(
            new GeneratedTestOutput(self::SOURCE, self::TARGET),
            GeneratedTestOutput::fromArray([
                'source' => self::SOURCE,
                'target' => self::TARGET,
            ])
        );
    }
}
