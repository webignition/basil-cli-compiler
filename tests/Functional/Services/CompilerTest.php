<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Functional\Services;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Model\CompiledTest;
use webignition\BasilCliCompiler\Services\Compiler;
use webignition\BasilCliCompiler\Tests\Services\ServiceMocker;
use webignition\BasilModels\Test\TestInterface;
use webignition\BasilParser\Test\TestParser;

class CompilerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider compileDataProvider
     *
     * @param TestInterface $test
     * @param string[]  $classNameFactoryClassNames
     * @param string $fullyQualifiedBaseClass
     * @param CompiledTest $expectedCompiledTest
     */
    public function testCompile(
        TestInterface $test,
        array $classNameFactoryClassNames,
        string $fullyQualifiedBaseClass,
        CompiledTest $expectedCompiledTest
    ) {
        $compiler = Compiler::createCompiler();

        $serviceMocker = new ServiceMocker();
        $compiler = $serviceMocker->mockClassNameFactoryOnCompiler($compiler, $classNameFactoryClassNames);

        self::assertEquals(
            $expectedCompiledTest,
            $compiler->compile($test, $fullyQualifiedBaseClass)
        );
    }

    public function compileDataProvider(): array
    {
        $testParser = TestParser::create();
        $test = $testParser->parse(
            [
                'config' => [
                    'browser' => 'chrome',
                    'url' => 'https://example.com/',
                ],
                'verify page is open' => [
                    'assertions' => [
                        '$page.url is "https://example.com/"',
                    ],
                ],
            ]
        )->withPath('tests/Fixtures/basil/Test/example.com.verify-open-literal.yml');

        return [
            'default' => [
                'test' => $test,
                'classNameFactoryClassNames' => [
                    'GeneratedVerifyOpenLiteralChrome',
                ],
                'fullyQualifiedBaseClass' => AbstractBaseTest::class,
                'expectedCompiledTest' => new CompiledTest(
                    $this->createExpectedCodeFromSource(
                        'tests/Fixtures/php/Test/GeneratedVerifyOpenLiteralChrome.php'
                    ),
                    'GeneratedVerifyOpenLiteralChrome'
                ),
            ],
        ];
    }

    private function createExpectedCodeFromSource(string $source): string
    {
        $content = (string) file_get_contents($source);

        $contentLines = explode("\n", $content);
        array_shift($contentLines);
        array_shift($contentLines);
        array_shift($contentLines);
        array_shift($contentLines);

        return trim(implode("\n", $contentLines));
    }
}
