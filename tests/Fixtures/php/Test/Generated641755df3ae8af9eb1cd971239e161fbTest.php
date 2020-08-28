<?php

namespace webignition\BasilCliCompiler\Generated;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BaseBasilTestCase\ClientManager;
use webignition\BasilModels\Test\Configuration;

class Generated641755df3ae8af9eb1cd971239e161fbTest extends AbstractBaseTest
{
    public static function setUpBeforeClass(): void
    {
        try {
            self::setBasilTestPath('tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml');
            self::setClientManager(new ClientManager(
                new Configuration(
                    'chrome',
                    'https://example.com/'
                )
            ));
            parent::setUpBeforeClass();
            self::$client->request('GET', 'https://example.com/');
        } catch (\Throwable $exception) {
            self::staticSetLastException($exception);
            self::fail('Exception raised during setUpBeforeClass()');
        }
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
