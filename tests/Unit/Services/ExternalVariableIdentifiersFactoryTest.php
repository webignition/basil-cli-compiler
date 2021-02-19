<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use webignition\BasilCliCompiler\Model\ExternalVariableIdentifiers;
use webignition\BasilCliCompiler\Services\ExternalVariableIdentifiersFactory;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;

class ExternalVariableIdentifiersFactoryTest extends AbstractBaseTest
{
    public function testCreate(): void
    {
        self::assertEquals(
            new ExternalVariableIdentifiers(
                '$this->navigator',
                '$_ENV',
                'self::$client',
                'self::$crawler',
                '$this',
                'self::$inspector',
                'self::$mutator',
                '$this->actionFactory',
                '$this->assertionFactory'
            ),
            ExternalVariableIdentifiersFactory::create()
        );
    }
}
