<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Functional\Services;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Services\ExternalVariableIdentifiersFactory;
use webignition\BasilCliCompiler\Services\PhpFileCreator;
use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;
use webignition\BasilCliCompiler\Services\TestWriter;
use webignition\BasilCompilableSourceFactory\ClassDefinitionFactory;
use webignition\BasilCompiler\Compiler;
use webignition\BasilModels\Test\TestInterface;
use webignition\BasilParser\Test\TestParser;

class TestWriterTest extends \PHPUnit\Framework\TestCase
{
    private TestWriter $testWriter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testWriter = new TestWriter(
            Compiler::create(ExternalVariableIdentifiersFactory::create()),
            new PhpFileCreator(),
            ClassDefinitionFactory::createFactory()
        );
    }

    /**
     * @dataProvider generateDataProvider
     */
    public function testGenerate(
        TestInterface $test,
        string $fullyQualifiedBaseClass,
        string $outputDirectory,
        string $expectedGeneratedCode
    ) {
        $generatedTestOutput = $this->testWriter->generate($test, $fullyQualifiedBaseClass, $outputDirectory);
        $expectedCodePath = $outputDirectory . '/' . $generatedTestOutput->getTarget();

        self::assertFileExists($expectedCodePath);
        self::assertFileIsReadable($expectedCodePath);

        self::assertEquals($expectedGeneratedCode, file_get_contents($expectedCodePath));

        if (file_exists($expectedCodePath)) {
            unlink($expectedCodePath);
        }
    }

    public function generateDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();
        $testParser = TestParser::create();

        return [
            'default' => [
                'test' => $testParser->parse(
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
                )->withPath('tests/Fixtures/basil/Test/example.com.verify-open-literal.yml'),
                'fullyQualifiedBaseClass' => AbstractBaseTest::class,
                'outputDirectory' => $root . '/tests/build/target',
                'expectedGeneratedCode' => (string) file_get_contents(
                    $root . '/tests/Fixtures/php/Test/Generated0233b88be49ad918bec797dcba9b01afTest.php'
                ),
            ],
        ];
    }
}
