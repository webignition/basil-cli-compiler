<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use Symfony\Component\Console\SingleCommandApplication;
use webignition\BasilCliCompiler\Services\ApplicationFactory;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;

class ApplicationFactoryTest extends AbstractBaseTest
{
    private ApplicationFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = ApplicationFactory::createFactory();
    }

    public function testCreate()
    {
        $application = $this->factory->create();

        $this->assertInstanceOf(SingleCommandApplication::class, $application);
    }
}
