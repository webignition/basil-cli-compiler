<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;

class ProjectRootPathProviderTest extends AbstractBaseTest
{
    public function testGet()
    {
        $expectedRoot = realpath(__DIR__ . '/../../..');

        $this->assertSame($expectedRoot, (new ProjectRootPathProvider())->get());
    }
}
