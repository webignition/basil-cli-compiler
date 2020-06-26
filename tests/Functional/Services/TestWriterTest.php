<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Functional\Services;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Services\ExternalVariableIdentifiersFactory;
use webignition\BasilCliCompiler\Services\PhpFileCreator;
use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;
use webignition\BasilCliCompiler\Services\TestWriter;
use webignition\BasilCompilableSource\ClassDefinition;
use webignition\BasilCompilableSource\ClassDefinitionInterface;
use webignition\BasilCompilableSourceFactory\ClassDefinitionFactory;
use webignition\BasilCompiler\Compiler;
use webignition\BasilModels\Test\TestInterface;
use webignition\BasilParser\Test\TestParser;
use webignition\ObjectReflector\ObjectReflector;

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
        string $generatedClassName,
        string $expectedGeneratedCode
    ) {
        $classDefinitionFactory = \Mockery::mock(ClassDefinitionFactory::createFactory());

        ObjectReflector::setProperty(
            $this->testWriter,
            TestWriter::class,
            'classDefinitionFactory',
            $classDefinitionFactory
        );

        $classDefinitionFactory
            ->shouldReceive('createClassDefinition')
            ->andReturn($this->createClassDefinitionWithClassName($test, $generatedClassName));

        $generatedTestOutput = $this->testWriter->generate($test, $fullyQualifiedBaseClass, $outputDirectory);
        $expectedCodePath = $outputDirectory . '/' . $generatedTestOutput->getTarget();

        self::assertFileExists($expectedCodePath);
        self::assertFileIsReadable($expectedCodePath);

        self::assertEquals($expectedGeneratedCode, file_get_contents($expectedCodePath));

        if (file_exists($expectedCodePath)) {
            unlink($expectedCodePath);
        }
    }

    private function createClassDefinitionWithClassName(
        TestInterface $test,
        string $className
    ): ClassDefinitionInterface {
        $classDefinitionFactory = ClassDefinitionFactory::createFactory();
        $classDefinition = $classDefinitionFactory->createClassDefinition($test);

        ObjectReflector::setProperty($classDefinition, ClassDefinition::class, 'name', $className);

        return $classDefinition;
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
                'generatedClassName' => 'ExampleComVerifyOpenLiteralTest',
                'expectedGeneratedCode' => (string) file_get_contents(
                    $root . '/tests/Fixtures/php/Test/ExampleComVerifyOpenLiteralTest.php'
                ),
            ],
        ];
    }
}
