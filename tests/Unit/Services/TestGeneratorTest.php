<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use webignition\BasilCliCompiler\Model\GeneratedTestOutput;
use webignition\BasilCliCompiler\Services\PhpFileCreator;
use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;
use webignition\BasilCliCompiler\Services\TestWriter;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;
use webignition\BasilCompiler\Compiler;
use webignition\BasilModels\Test\TestInterface;
use webignition\BasilParser\Test\TestParser;

class TestGeneratorTest extends AbstractBaseTest
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
        $compiler = \Mockery::mock(Compiler::class);
        $compiler
            ->shouldReceive('createClassName')
            ->with($test)
            ->andReturn($generatedClassName);

        $compiledCode = '<?php echo "compiled";';

        $compiler
            ->shouldReceive('compile')
            ->with($test, $fullyQualifiedBaseClass)
            ->andReturn($compiledCode);

        $phpFileCreator = \Mockery::mock(PhpFileCreator::class);
        $phpFileCreator
            ->shouldReceive('setOutputDirectory')
            ->with($outputDirectory);

        $phpFileCreator
            ->shouldReceive('create')
            ->with($generatedClassName, $compiledCode)
            ->andReturn($generatedClassName . '.php');

        $testGenerator = new TestWriter($compiler, $phpFileCreator);

        $generatedTestOutput = $testGenerator->generate($test, $fullyQualifiedBaseClass, $outputDirectory);

        $this->assertEquals($expectedGeneratedTestOutput, $generatedTestOutput);
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
