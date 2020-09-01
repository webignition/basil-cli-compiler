<?php

namespace webignition\BasilCliCompiler\Generated;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BaseBasilTestCase\ClientManager;
use webignition\BasilModels\Test\Configuration;
use webignition\DomElementIdentifier\ElementIdentifier;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidLocatorException;

class Generated1a8ee6813e6fc3bf6de1ddbb4aaf6115Test extends AbstractBaseTest
{
    public static function setUpBeforeClass(): void
    {
        try {
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
        $this->setBasilStepName('follow more information');
        $this->setCurrentDataSet(null);

        // $"a" exists <- click $"a"
        $this->handledStatements[] = $this->assertionFactory->createFromJson('{
            "container": {
                "type": "derived-value-operation-assertion",
                "value": "$\\"a\\"",
                "operator": "exists"
            },
            "statement": {
                "statement-type": "action",
                "source": "click $\\"a\\"",
                "type": "click",
                "arguments": "$\\"a\\"",
                "identifier": "$\\"a\\""
            }
        }');
        $this->examinedElementIdentifier = ElementIdentifier::fromJson('{
            "locator": "a"
        }');
        try {
            $this->setBooleanExaminedValue(
                $this->navigator->hasOne($this->examinedElementIdentifier)
            );
        } catch (InvalidLocatorException $exception) {
            self::staticSetLastException($exception);
            $this->fail('Invalid locator');
        }
        $this->assertTrue(
            $this->getBooleanExaminedValue()
        );

        // click $"a"
        $this->handledStatements[] = $this->actionFactory->createFromJson('{
            "statement-type": "action",
            "source": "click $\\"a\\"",
            "type": "click",
            "arguments": "$\\"a\\"",
            "identifier": "$\\"a\\""
        }');
        (function () {
            $element = $this->navigator->findOne(ElementIdentifier::fromJson('{
                "locator": "a"
            }'));
            $element->click();
        })();
        $this->refreshCrawlerAndNavigator();

        // $page.url is "https://www.iana.org/domains/reserved"
        $this->handledStatements[] = $this->assertionFactory->createFromJson('{
            "statement-type": "assertion",
            "source": "$page.url is \\"https:\\/\\/www.iana.org\\/domains\\/reserved\\"",
            "identifier": "$page.url",
            "operator": "is",
            "value": "\\"https:\\/\\/www.iana.org\\/domains\\/reserved\\""
        }');
        $this->setExpectedValue("https://www.iana.org/domains/reserved" ?? null);
        $this->setExaminedValue(self::$client->getCurrentURL() ?? null);
        $this->assertEquals(
            $this->getExpectedValue(),
            $this->getExaminedValue()
        );
    }
}
