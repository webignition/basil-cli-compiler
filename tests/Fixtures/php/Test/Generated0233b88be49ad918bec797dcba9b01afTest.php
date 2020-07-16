<?php

namespace webignition\BasilCliCompiler\Generated;

use webignition\BaseBasilTestCase\AbstractBaseTest;

class Generated0233b88be49ad918bec797dcba9b01afTest extends AbstractBaseTest
{
    public static function setUpBeforeClass(): void
    {
        self::setUpClient(0);
        parent::setUpBeforeClass();
        self::$client->request('GET', 'https://example.com/');
        self::setBasilTestPath('tests/Fixtures/basil/Test/example.com.verify-open-literal.yml');
    }

    public function test1()
    {
        $this->setBasilStepName('verify page is open');
        $this->setCurrentDataSet(null);

        // $page.url is "https://example.com/"
        $this->handledStatements[] = $this->assertionFactory->createFromJson('{
            "statement-type": "assertion",
            "source": "$page.url is \\"https:\\/\\/example.com\\/\\"",
            "identifier": "$page.url",
            "operator": "is",
            "value": "\\"https:\\/\\/example.com\\/\\""
        }');
        $this->setExpectedValue("https://example.com/" ?? null);
        $this->setExaminedValue(self::$client->getCurrentURL() ?? null);
        $this->assertEquals(
            $this->getExpectedValue(),
            $this->getExaminedValue()
        );
    }
}
