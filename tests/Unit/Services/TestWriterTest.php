<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use webignition\BasilCliCompiler\Model\GeneratedTestOutput;
use webignition\BasilCliCompiler\Services\PhpFileCreator;
use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;
use webignition\BasilCliCompiler\Services\TestWriter;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;
use webignition\BasilCompilableSource\ClassDefinition;
use webignition\BasilCompilableSource\Expression\ClassDependency;
use webignition\BasilCompilableSourceFactory\ClassDefinitionFactory;
use webignition\BasilCompiler\Compiler;
use webignition\BasilModels\Test\TestInterface;
use webignition\BasilParser\Test\TestParser;

class TestWriterTest extends AbstractBaseTest
{
    /**
     * @dataProvider generateDataProvider
     */
    public function testGenerate(
        TestInterface $test,
        string $fullyQualifiedBaseClass,
        string $outputDirectory,
        string $generatedClassName,
        GeneratedTestOutput $expectedGeneratedTestOutput
    ) {
        $classDefinition = \Mockery::mock(ClassDefinition::class);
        $classDefinition
            ->shouldReceive('setBaseClass')
            ->withArgs(function (ClassDependency $classDependency) use ($fullyQualifiedBaseClass) {
                self::assertEquals($classDependency, new ClassDependency($fullyQualifiedBaseClass));

                return true;
            });
        $classDefinition
            ->shouldReceive('getName')
            ->andReturn($generatedClassName);

        $classDefinitionFactory = \Mockery::mock(ClassDefinitionFactory::createFactory());
        $classDefinitionFactory
            ->shouldReceive('createClassDefinition')
            ->with($test)
            ->andReturn($classDefinition);

        $compiledCode = '<?php echo "compiled";';

        $compiler = \Mockery::mock(Compiler::class);
        $compiler
            ->shouldReceive('compile')
            ->with($classDefinition)
            ->andReturn($compiledCode);

        $phpFileCreator = \Mockery::mock(PhpFileCreator::class);
        $phpFileCreator
            ->shouldReceive('setOutputDirectory')
            ->with($outputDirectory);

        $phpFileCreator
            ->shouldReceive('create')
            ->with($generatedClassName, $compiledCode)
            ->andReturn($generatedClassName . '.php');

        $testWriter = new TestWriter($compiler, $phpFileCreator, $classDefinitionFactory);

        $generatedTestOutput = $testWriter->generate($test, $fullyQualifiedBaseClass, $outputDirectory);

        self::assertEquals($expectedGeneratedTestOutput, $generatedTestOutput);
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
                            'url' => 'http://example.com',
                        ],
                        'verify page is open' => [
                            'assertions' => [
                                '$page.url is "https://example.com"',
                            ],
                        ],
                    ]
                )->withPath($root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml'),
                'fullyQualifiedBaseClass' => AbstractBaseTest::class,
                'outputDirectory' => $root . '/tests/build/target',
                'generatedClassName' => 'ExampleComVerifyOpenLiteralTest',
                'expectedGeneratedTestOutput' => new GeneratedTestOutput(
                    $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    'ExampleComVerifyOpenLiteralTest.php'
                ),
            ],
        ];
    }
}
