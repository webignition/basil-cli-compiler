<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Functional\Services;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Model\CompiledTest;
use webignition\BasilCliCompiler\Services\Compiler;
use webignition\BasilModels\Test\TestInterface;
use webignition\BasilParser\Test\TestParser;

class CompilerTest extends \PHPUnit\Framework\TestCase
{
    private Compiler $compiler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->compiler = Compiler::createCompiler();
    }

    /**
     * @dataProvider compileDataProvider
     */
    public function testCompile(
        TestInterface $test,
        string $fullyQualifiedBaseClass,
        CompiledTest $expectedCompiledTest
    ) {
        self::assertEquals(
            $expectedCompiledTest,
            $this->compiler->compile($test, $fullyQualifiedBaseClass)
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
                'fullyQualifiedBaseClass' => AbstractBaseTest::class,
                'expectedCompiledTest' => new CompiledTest(
                    $test,
                    $this->createExpectedCodeFromSource(
                        'tests/Fixtures/php/Test/Generated0233b88be49ad918bec797dcba9b01afTest.php'
                    ),
                    'Generated0233b88be49ad918bec797dcba9b01afTest'
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
