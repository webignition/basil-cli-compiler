<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use webignition\BasilCliCompiler\Services\ExternalVariableIdentifiersFactory;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;
use webignition\BasilCompiler\ExternalVariableIdentifiers;

class ExternalVariableIdentifiersFactoryTest extends AbstractBaseTest
{
    public function testCreate()
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
